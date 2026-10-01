<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Extraction\PdftotextPdfTextExtractor;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionPdfImportJobRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

[$script, $jobId] = array_pad($argv, 2, null);
$dryRun = in_array('--dry-run', $argv, true);
if (!is_string($jobId) || $jobId === '') throw new InvalidArgumentException('Informe o identificador do job.');
$em = DoctrineEntityManagerFactory::create();
$job = $em->find(QuestionPdfImportJobRecord::class, $jobId);
if (!$job instanceof QuestionPdfImportJobRecord) throw new RuntimeException('Job não encontrado.');
$pages = (new PdftotextPdfTextExtractor())->extractPages($job->documentPath);
$normalizedPages = array_map(normalize(...), $pages);
$questions = $em->createQueryBuilder()->select('question')->from(QuestionRecord::class, 'question')->where('question.sourcePdfJobId = :job')->setParameter('job', $jobId)->getQuery()->getResult();
$result = ['questions' => count($questions), 'located' => 0, 'board_updated' => 0, 'official_answer_updated' => 0, 'unresolved' => []];
foreach ($questions as $question) {
    if (!$question instanceof QuestionRecord) continue;
    $candidatePages = locate($normalizedPages, $question->statement);
    if ($candidatePages === []) { $result['unresolved'][] = $question->id; continue; }
    $result['located']++;
    $boards = [];
    $answers = [];
    foreach ($candidatePages as $page) {
        $board = board(implode("\n", array_slice($pages, max(0, $page - 2), 4)));
        if ($board !== null) $boards[$board] = true;
        $answer = answer(implode("\n", array_slice($pages, $page, 4)));
        if ($answer !== null) $answers[$answer] = true;
    }
    $board = count($boards) === 1 ? array_key_first($boards) : null;
    if ($board !== null && $question->board !== $board) { if (!$dryRun) $question->board = $board; $result['board_updated']++; }
    $answer = count($answers) === 1 ? array_key_first($answers) : null;
    if ($answer !== null && $question->correctOptionId === null) {
        $option = $em->createQueryBuilder()->select('option')->from(QuestionOptionRecord::class, 'option')->where('option.questionId = :question')->andWhere('option.label = :label')->setParameter('question', $question->id)->setParameter('label', $answer)->getQuery()->getOneOrNullResult();
        if ($option instanceof QuestionOptionRecord) { if (!$dryRun) { $question->correctOptionId = $option->id; $question->answerKeySource = 'OFFICIAL'; } $result['official_answer_updated']++; }
    }
    if (!$dryRun && ($board !== null || $answer !== null)) {
        $question->sourcePdfPages = array_values(array_unique(array_merge((array) $question->sourcePdfPages, array_map(static fn (int $page): int => $page + 1, $candidatePages))));
        sort($question->sourcePdfPages, SORT_NUMERIC);
        $question->updatedAt = new DateTimeImmutable('now');
    }
}
if (!$dryRun) $em->flush();
$result['unresolved'] = ['count' => count($result['unresolved']), 'sample_ids' => array_slice($result['unresolved'], 0, 20)];
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;

function normalize(string $value): string { $value = mb_strtolower($value); $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value; $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? ''; return trim((string) preg_replace('/\s+/', ' ', $value)); }
function locate(array $pages, string $statement): array { $normalized = normalize($statement); foreach ([110, 85, 65, 48] as $length) { $needle = mb_substr($normalized, 0, $length); if (mb_strlen($needle) < $length) continue; $matches = []; foreach ($pages as $index => $page) if (mb_strpos($page, $needle) !== false) $matches[] = $index; if ($matches !== []) return $matches; } return []; }
function board(string $section): ?string { preg_match_all('/\b(CEBRASPE|CESPE|FCC|FGV|CESGRANRIO|VUNESP|QUADRIX|IBFC|AOCP|CONSULPLAN|FUNDATEC|IADES|ESAF)\b/iu', $section, $matches); if (($matches[1] ?? []) === []) return null; return strtoupper((string) end($matches[1])); }
function answer(string $section): ?string { $next = preg_split('/\n\s*(?:\d{1,3}[.)-]|Quest[aã]o\s+\d+)/iu', $section, 2)[0] ?? $section; return preg_match('/\bGabarito\s*:\s*(?:Letra\s*)?([A-E])\b/iu', $next, $match) === 1 ? strtoupper($match[1]) : null; }
