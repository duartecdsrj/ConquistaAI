<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
use App\Infrastructure\Extraction\PdfimagesQuestionPdfAssetExtractor;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionDuplicateDetector;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfQuestionWriter;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionTaxonomyAssignmentRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionPdfImportJobRecord;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\DoctrineTaxonomySubjectRepository;

[$script, $jsonPath, $pdfPath] = array_pad($argv, 3, null);
$dryRun = in_array('--dry-run', $argv, true);
if (!is_string($jsonPath) || !is_file($jsonPath) || !is_string($pdfPath) || !is_file($pdfPath)) {
    throw new InvalidArgumentException('Informe os caminhos existentes do JSON e do PDF.');
}

$payload = json_decode((string) file_get_contents($jsonPath), true, 512, JSON_THROW_ON_ERROR);
if (!is_array($payload) || !is_array($payload['questions'] ?? null)) throw new InvalidArgumentException('JSON de questões inválido.');
$exam = is_array($payload['exam'] ?? null) ? $payload['exam'] : [];
$source = implode(' — ', array_values(array_filter([
    stringValue($exam['contest'] ?? null),
    stringValue($exam['position'] ?? null),
], static fn (?string $value): bool => $value !== null)));
$board = stringValue($exam['organizer'] ?? null);
$year = is_int($exam['year'] ?? null) ? $exam['year'] : null;

$questions = [];
$imagePages = [];
$rejected = 0;
foreach ($payload['questions'] as $item) {
    $normalized = normalizeQuestion($item, $board, $year, $source);
    if ($normalized === null) {
        $rejected++;
        continue;
    }
    foreach ($normalized['image_pages'] as $page) $imagePages[$page] = true;
    $questions[] = $normalized;
}

$summary = ['input' => count($payload['questions']), 'eligible' => count($questions), 'rejected' => $rejected, 'image_pages' => array_keys($imagePages)];
if ($dryRun) {
    echo json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
    exit;
}

$em = DoctrineEntityManagerFactory::create();
$admin = $em->getConnection()->fetchOne("SELECT id FROM users WHERE email = 'mvp-admin@concursos.test' AND status = 'ACTIVE' LIMIT 1");
if (!is_string($admin) || $admin === '') throw new RuntimeException('Administrador de importação indisponível.');
$job = new QuestionPdfImportJobRecord();
$job->id = uuid();
$job->syllabusId = null;
$job->createdBy = $admin;
$job->documentPath = $pdfPath;
$job->documentSha256 = hash_file('sha256', $pdfPath);
$job->documentOriginalName = basename($pdfPath);
$job->algorithmVersion = 'json-pdf-reference-assets-v1';
$job->status = 'PROCESSING';
$job->progress = 12;
$pdfInfo = (string) shell_exec('pdfinfo ' . escapeshellarg($pdfPath));
$job->pageCount = preg_match('/^Pages:\s*(\d+)/m', $pdfInfo, $pagesMatch) === 1 ? (int) $pagesMatch[1] : 0;
$job->candidatePages = count($questions);
$job->extractedQuestions = count($questions);
$job->createdAt = new DateTimeImmutable('now');
$job->startedAt = $job->createdAt;
$em->persist($job);
$em->flush();

$assets = (new PdfimagesQuestionPdfAssetExtractor())->extract($pdfPath, $job->documentSha256, array_keys($imagePages));
foreach ($questions as &$question) {
    $verified = [];
    foreach ($question['image_pages'] as $page) {
        foreach ($assets[$page] ?? [] as $path) $verified[] = ['path' => $path, 'page' => $page];
        if (($assets[$page] ?? []) === []) {
            $render = '/app/storage/question-pdf-assets/' . $job->documentSha256 . '/page-' . $page . '-render.png';
            if (is_file($render)) $verified[] = ['path' => $render, 'page' => $page];
        }
    }
    $question['verified_assets'] = $verified;
}
unset($question);

$writer = new DoctrineQuestionPdfQuestionWriter(
    $em,
    new DoctrineQuestionDuplicateDetector($em),
    new DoctrineTaxonomySubjectRepository($em),
    new DoctrineQuestionTaxonomyAssignmentRepository($em),
    new SubjectTaxonomyService(),
    new ImportedQuestionContentSanitizer(),
);
$result = ['created' => 0, 'duplicates' => 0, 'classified' => 0, 'failed' => 0, 'createdSubjects' => 0];
foreach ($questions as $question) {
    $partial = $writer->write($job->createdBy, [$question], [], $job->id);
    foreach ($result as $key => $value) $result[$key] += $partial[$key];
}
$job->status = 'COMPLETED';
$job->progress = 100;
$job->processedChunks = count($questions);
$job->classifiedQuestions = $result['classified'];
$job->createdQuestions = $result['created'];
$job->duplicateQuestions = $result['duplicates'];
$job->failedQuestions = $result['failed'];
$job->createdTaxonomySubjects = $result['createdSubjects'];
$job->errorMessage = sprintf('Importação JSON com ativos de referência do PDF: %d registros estruturais foram recusados antes da escrita.', $rejected);
$job->finishedAt = new DateTimeImmutable('now');
$em->flush();
echo json_encode($summary + ['job_id' => $job->id, 'result' => $result], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;

function normalizeQuestion(mixed $item, ?string $board, ?int $year, string $source): ?array
{
    if (!is_array($item) || !is_string($item['statement'] ?? null) || trim($item['statement']) === '' || !is_array($item['alternatives'] ?? null)) return null;
    $alternatives = [];
    foreach ($item['alternatives'] as $alternative) {
        if (!is_array($alternative) || !is_string($alternative['letter'] ?? null) || !is_string($alternative['text'] ?? null)) return null;
        $label = strtoupper(trim($alternative['letter']));
        $text = trim($alternative['text']);
        if (!in_array($label, ['A', 'B', 'C', 'D', 'E'], true) || $text === '' || isset($alternatives[$label])) return null;
        $alternatives[$label] = ['content' => $text];
    }
    $labels = array_keys($alternatives);
    $expected = count($labels) === 4 ? ['A', 'B', 'C', 'D'] : (count($labels) === 5 ? ['A', 'B', 'C', 'D', 'E'] : []);
    if ($expected === [] || $labels !== $expected) return null;
    $pages = [];
    foreach ((array) ($item['images'] ?? []) as $image) {
        $page = is_array($image) ? ($image['page'] ?? null) : null;
        if (is_int($page) && $page > 0) $pages[$page] = true;
    }
    return [
        'type' => 'MULTIPLE_CHOICE',
        'statement' => trim($item['statement']),
        'options' => array_values($alternatives),
        'correct_option' => null,
        'board' => $board,
        'exam' => $source === '' ? null : $source,
        'year' => $year,
        'taxonomy_path' => ['Tecnologia da Informação', 'Redes de Computadores', 'Fundamentos de Redes'],
        'parent_subject' => 'Redes de Computadores',
        'difficulty' => 'MEDIUM',
        'pages' => array_keys($pages),
        'image_pages' => array_keys($pages),
    ];
}

function stringValue(mixed $value): ?string { return is_string($value) && trim($value) !== '' ? trim($value) : null; }
function uuid(): string { $bytes = random_bytes(16); $bytes[6] = chr((ord($bytes[6]) & 15) | 64); $bytes[8] = chr((ord($bytes[8]) & 63) | 128); return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4)); }
