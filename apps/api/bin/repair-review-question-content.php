<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

$apply = in_array('--apply', $argv, true);
$jobId = $argv[1] ?? '';
if ($jobId === '' || str_starts_with($jobId, '--')) {
    fwrite(STDERR, "Uso: php bin/repair-review-question-content.php <job-id> [--apply]\n");
    exit(1);
}

$em = DoctrineEntityManagerFactory::create();
$sanitizer = new ImportedQuestionContentSanitizer();
/** @var list<QuestionRecord> $questions */
$questions = $em->createQueryBuilder()->select('question')
    ->from(QuestionRecord::class, 'question')
    ->where('question.sourcePdfJobId = :jobId')
    ->andWhere('question.status = :status')
    ->setParameter('jobId', $jobId)
    ->setParameter('status', 'REVIEW')
    ->orderBy('question.createdAt', 'ASC')
    ->getQuery()->getResult();

$rows = [];
$changedStatements = $changedOptions = $invalid = $duplicates = $answerTransfers = 0;
$invalidReasons = ['short_statement' => 0, 'option_count' => 0, 'empty_option' => 0, 'artifact' => 0, 'truncated_start' => 0];
foreach ($questions as $question) {
    /** @var list<QuestionOptionRecord> $options */
    $options = $em->createQueryBuilder()->select('option')
        ->from(QuestionOptionRecord::class, 'option')
        ->where('option.questionId = :questionId')
        ->orderBy('option.sortOrder', 'ASC')
        ->setParameter('questionId', $question->id)
        ->getQuery()->getResult();

    $rawOptions = array_map(static fn (QuestionOptionRecord $option): array => ['content' => $option->content], $options);
    $statement = $sanitizer->statement($question->statement, $rawOptions);
    $contents = array_map(fn (QuestionOptionRecord $option): string => $sanitizer->option($option->content), $options);
    $statementChanged = $statement !== $question->statement;
    $optionChanges = 0;
    foreach ($options as $index => $option) if ($contents[$index] !== $option->content) $optionChanges++;

    $shortStatement = mb_strlen($statement) < 35;
    $wrongOptionCount = count($options) < 3 || count($options) > 5;
    $emptyOption = count(array_filter($contents, static fn (string $content): bool => trim($content) === '')) > 0;
    $artifact = hasExtractionArtifact($statement . "\n" . implode("\n", $contents));
    $truncatedStart = preg_match('/^\p{Ll}/u', $statement) === 1;
    if ($shortStatement) $invalidReasons['short_statement']++;
    if ($wrongOptionCount) $invalidReasons['option_count']++;
    if ($emptyOption) $invalidReasons['empty_option']++;
    if ($artifact) $invalidReasons['artifact']++;
    if ($truncatedStart) $invalidReasons['truncated_start']++;
    $isInvalid = $shortStatement || $wrongOptionCount || $emptyOption || $artifact || $truncatedStart;
    if ($isInvalid) $invalid++;
    if ($statementChanged) $changedStatements++;
    $changedOptions += $optionChanges;
    $rows[] = compact('question', 'options', 'statement', 'contents', 'statementChanged', 'optionChanges', 'isInvalid');
}

$canonical = [];
foreach ($rows as $index => $row) {
    if ($row['isInvalid']) continue;
    $key = normalize($row['statement']) . '|' . implode('|', array_map('normalize', $row['contents']));
    $canonical[$key][] = $index;
}
$voidDuplicates = [];
foreach ($canonical as $indexes) {
    if (count($indexes) < 2) continue;
    usort($indexes, static function (int $left, int $right) use ($rows): int {
        $leftScore = ($rows[$left]['statementChanged'] ? 1 : 0) + $rows[$left]['optionChanges'];
        $rightScore = ($rows[$right]['statementChanged'] ? 1 : 0) + $rows[$right]['optionChanges'];
        if ($leftScore !== $rightScore) return $leftScore <=> $rightScore;
        $leftAnswer = $rows[$left]['question']->correctOptionId === null ? 1 : 0;
        $rightAnswer = $rows[$right]['question']->correctOptionId === null ? 1 : 0;
        return $leftAnswer <=> $rightAnswer;
    });
    $winner = array_shift($indexes);
    $answerLabels = array_values(array_unique(array_filter(array_merge([correctLabel($rows[$winner])], array_map(static fn (int $index): ?string => correctLabel($rows[$index]), $indexes)))));
    if ($rows[$winner]['question']->correctOptionId === null && count($answerLabels) === 1) {
        foreach ($rows[$winner]['options'] as $option) {
            if ($option->label === $answerLabels[0]) {
                $rows[$winner]['question']->correctOptionId = $option->id;
                $rows[$winner]['question']->answerKeySource = 'OFFICIAL';
                $answerTransfers++;
                break;
            }
        }
    }
    foreach ($indexes as $index) $voidDuplicates[$index] = true;
}
$duplicates = count($voidDuplicates);

if ($apply) {
    $now = new DateTimeImmutable('now');
    foreach ($rows as $index => $row) {
        /** @var QuestionRecord $question */
        $question = $row['question'];
        if (isset($voidDuplicates[$index]) || $row['isInvalid']) {
            $question->status = 'VOID';
            $question->updatedAt = $now;
            continue;
        }
        $question->statement = $row['statement'];
        $question->updatedAt = $now;
        foreach ($row['options'] as $optionIndex => $option) $option->content = $row['contents'][$optionIndex];
    }
    $em->flush();
}

echo json_encode([
    'mode' => $apply ? 'applied' : 'dry-run',
    'reviewed' => count($rows),
    'changed_statements' => $changedStatements,
    'changed_options' => $changedOptions,
    'void_invalid' => $invalid,
    'invalid_reasons' => $invalidReasons,
    'void_duplicates' => $duplicates,
    'answer_keys_transferred_from_duplicates' => $answerTransfers,
    'kept_for_review' => count($rows) - $invalid - $duplicates,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;

function normalize(string $value): string
{
    return mb_strtolower((string) preg_replace('/\s+/u', ' ', trim($value)));
}

function hasExtractionArtifact(string $value): bool
{
    return str_contains($value, 'Concursos da Área Fiscal Especialidade TI - Arquitetura e Sistemas Operacionais')
        || str_contains($value, 'Evandro Dalla Vecchia, Equipe Informática e TI')
        || str_contains($value, 'Eletronica Em Arte')
        || str_contains($value, 'Licensed to ')
        || preg_match('/==[0-9a-f]{6,}==/iu', $value) === 1;
}

/** @param array{question: QuestionRecord, options: list<QuestionOptionRecord>} $row */
function correctLabel(array $row): ?string
{
    $id = $row['question']->correctOptionId;
    if ($id === null) return null;
    foreach ($row['options'] as $option) if ($option->id === $id) return $option->label;
    return null;
}
