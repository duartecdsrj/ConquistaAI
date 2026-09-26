<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Extraction\PdftotextPdfTextExtractor;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionPdfImportJobRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

$jobId = $argv[1] ?? '';
$dryRun = in_array('--dry-run', $argv, true);
$entityManager = DoctrineEntityManagerFactory::create();
$job = $entityManager->find(QuestionPdfImportJobRecord::class, $jobId);
if (!$job instanceof QuestionPdfImportJobRecord) throw new RuntimeException('Job não encontrado.');

$pages = (new PdftotextPdfTextExtractor())->extractPages($job->documentPath);
$questions = $entityManager->createQueryBuilder()->select('question')->from(QuestionRecord::class, 'question')
    ->where('question.sourcePdfJobId=:job')->andWhere('question.correctOptionId IS NULL')->setParameter('job', $jobId)
    ->getQuery()->getResult();
$found = [];
foreach ($questions as $question) {
    if (!$question instanceof QuestionRecord) continue;
    $page = (int) (($question->sourcePdfPages ?? [1])[0] ?? 1);
    $context = implode("\n", array_slice($pages, max(0, $page - 1), 16));
    $match = answerFromNearbyTable($context, $question->statement);
    if ($match === null) continue;
    $option = $entityManager->createQueryBuilder()->select('option')->from(QuestionOptionRecord::class, 'option')
        ->where('option.questionId=:question')->andWhere('option.label=:label')
        ->setParameter('question', $question->id)->setParameter('label', $match['answer'])->getQuery()->getOneOrNullResult();
    if (!$option instanceof QuestionOptionRecord) continue;
    $found[] = ['question' => $question, 'option' => $option, 'number' => $match['number'], 'answer' => $match['answer']];
}

if ($dryRun) {
    echo json_encode(['candidates' => count($questions), 'recoverable_official' => count($found), 'sample' => array_map(static fn (array $item): array => ['id' => $item['question']->id, 'number' => $item['number'], 'answer' => $item['answer']], array_slice($found, 0, 10))], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
    exit;
}
foreach ($found as $item) {
    $item['question']->correctOptionId = $item['option']->id;
    $item['question']->answerKeySource = 'OFFICIAL';
    $item['question']->updatedAt = new DateTimeImmutable('now');
}
$entityManager->flush();
echo json_encode(['updated_official' => count($found), 'unresolved' => count($questions) - count($found)], JSON_UNESCAPED_UNICODE) . PHP_EOL;

/** @return array{number:int,answer:string}|null */
function answerFromNearbyTable(string $context, string $statement): ?array
{
    $statementOffset = statementOffset($context, $statement);
    if ($statementOffset === null) return null;
    $prefix = substr($context, 0, $statementOffset);
    $number = questionNumberBefore($prefix);
    if ($number === null) return null;
    $tableOffset = stripos($context, 'GABARITO', $statementOffset);
    if ($tableOffset === false) return null;
    return answerInTable(substr($context, $tableOffset, 2800), $number);
}

function statementOffset(string $context, string $statement): ?int
{
    $words = array_values(array_filter(preg_split('/\s+/u', trim($statement)) ?: [], static fn (string $word): bool => mb_strlen($word) > 1));
    if (count($words) < 5) return null;
    $pattern = implode('\\s+', array_map(static fn (string $word): string => preg_quote($word, '/'), array_slice($words, 0, 8)));
    return preg_match('/'.$pattern.'/iu', $context, $match, PREG_OFFSET_CAPTURE) === 1 ? $match[0][1] : null;
}

function questionNumberBefore(string $prefix): ?int
{
    preg_match_all('/(?:^|\n)\h*(?<number>\d{1,3})\.\h+\(?\h*(?:FGV|FCC|CESGRANRIO|VUNESP|QUADRIX|IBFC|AOCP|CONSULPLAN|CEBRASPE|CESPE|FUNDATEC|IADES|COPEVE|COMPERVE|ESAF)\b/iu', $prefix, $matches);
    if (($matches['number'] ?? []) === []) return null;
    return (int) $matches['number'][array_key_last($matches['number'])];
}

/** @return array{number:int,answer:string}|null */
function answerInTable(string $table, int $number): ?array
{
    $lines = preg_split('/\R/u', $table) ?: [];
    for ($index = 0, $total = count($lines); $index < $total; $index++) {
        preg_match_all('/\b\d{1,3}\b/u', $lines[$index], $numbers);
        if (count($numbers[0] ?? []) < 3) continue;
        for ($answerLine = $index + 1; $answerLine < min($total, $index + 4); $answerLine++) {
            preg_match_all('/\b[A-E]\b/u', $lines[$answerLine], $answers);
            if (count($answers[0] ?? []) < count($numbers[0])) continue;
            foreach ($numbers[0] as $position => $candidate) {
                if ((int) $candidate === $number) return ['number' => $number, 'answer' => $answers[0][$position]];
            }
        }
    }
    return null;
}
