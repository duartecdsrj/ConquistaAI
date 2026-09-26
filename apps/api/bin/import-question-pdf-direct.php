<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
use App\Infrastructure\Extraction\PdftotextPdfTextExtractor;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionDuplicateDetector;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfQuestionWriter;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionTaxonomyAssignmentRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionPdfImportJobRecord;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\DoctrineTaxonomySubjectRepository;

$jobId = $argv[1] ?? '';
$em = DoctrineEntityManagerFactory::create();
$job = $em->find(QuestionPdfImportJobRecord::class, $jobId);
if (!$job instanceof QuestionPdfImportJobRecord) throw new RuntimeException('Job não encontrado.');
$taxonomy = new DoctrineTaxonomySubjectRepository($em);
$writer = new DoctrineQuestionPdfQuestionWriter($em, new DoctrineQuestionDuplicateDetector($em), $taxonomy, new DoctrineQuestionTaxonomyAssignmentRepository($em), new SubjectTaxonomyService(), new ImportedQuestionContentSanitizer());
$pages = (new PdftotextPdfTextExtractor())->extractPages($job->documentPath);
$questions = [];
foreach ($pages as $index => $text) {
    preg_match_all('/Ano:\s*(?<year>20\d{2}).{0,220}?Banca:\s*(?<board>.+?)(?=\s+Órgão:).{0,800}?(?<statement>[^\n].{40,2500}?)\n\s*a\)\s*(?<a>.+?)\n\s*b\)\s*(?<b>.+?)\n\s*c\)\s*(?<c>.+?)\n\s*d\)\s*(?<d>.+?)\n\s*e\)\s*(?<e>.+?)(?=\n\s*(?:Gabarito|Coment|Ano:|Quest|[0-9]+\.|$))/isu', $text, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $statement = trim($match['statement']);
        $normalized = mb_strtolower($statement);
        if (str_contains($normalized, 'sql')) { $parent = 'Banco de Dados'; $leaf = 'SQL'; }
        elseif (str_contains($normalized, 'relacion') || str_contains($normalized, 'entidade')) { $parent = 'Banco de Dados'; $leaf = 'Modelagem de Dados'; }
        elseif (str_contains($normalized, 'estruturad') || str_contains($normalized, 'dado')) { $parent = 'Dados'; $leaf = 'Dados Estruturados e Não Estruturados'; }
        else { $parent = 'Banco de Dados'; $leaf = 'Conceitos de Banco de Dados'; }
        $board = trim((string) preg_replace('/\s+/u', ' ', $match['board']));
        $board = (string) preg_replace('/^(C|F|V)\s+(ESPE|CC|UNESP)/i', '$1$2', $board);
        $source = implode("\n", array_slice($pages, $index, 3));
        $normalizedSource = preg_replace('/\s+/u', ' ', $source) ?? '';
        $needle = mb_substr((string) (preg_replace('/\s+/u', ' ', $statement) ?? ''), 0, 90);
        $offset = mb_stripos($normalizedSource, $needle);
        $answer = null;
        if ($offset !== false && preg_match('/Gabarito:\s*([A-E])\b/iu', mb_substr($normalizedSource, $offset), $answerMatch)) $answer = strtoupper($answerMatch[1]);
        $options = [];
        foreach (['a', 'b', 'c', 'd', 'e'] as $label) $options[] = ['content' => trim($match[$label])];
        $questions[] = [
            'type' => 'MULTIPLE_CHOICE', 'statement' => $statement, 'options' => $options, 'correct_option' => $answer, 'answer_key_source' => $answer === null ? null : 'OFFICIAL',
            'board' => $board, 'exam' => null, 'year' => (int) $match['year'],
            'taxonomy_path' => ['Tecnologia da Informação', $parent, $leaf], 'parent_subject' => $parent,
            'pages' => [$index + 1], 'difficulty' => mb_strlen($statement) > 850 ? 'HARD' : (mb_strlen($statement) > 380 ? 'MEDIUM' : 'EASY'),
        ];
    }
}
$result = ['created' => 0, 'duplicates' => 0, 'classified' => 0, 'failed' => 0, 'createdSubjects' => 0];
foreach ($questions as $question) {
    $partial = $writer->write($job->createdBy, [$question], [], $job->id);
    foreach ($result as $key => $value) $result[$key] += $partial[$key];
}
$job->status = 'COMPLETED'; $job->progress = 100; $job->pageCount = count($pages); $job->candidatePages = count($pages);
$job->extractedQuestions = count($questions); $job->classifiedQuestions = $result['classified']; $job->createdQuestions = $result['created']; $job->duplicateQuestions = $result['duplicates']; $job->failedQuestions = $result['failed']; $job->createdTaxonomySubjects = $result['createdSubjects'];
$job->errorMessage = 'Extração direta determinística; metadados apenas quando explícitos no PDF.'; $job->finishedAt = new DateTimeImmutable('now');
$em->flush();
echo json_encode(['extracted' => count($questions), 'result' => $result], JSON_UNESCAPED_UNICODE) . PHP_EOL;
