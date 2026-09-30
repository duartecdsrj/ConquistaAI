<?php
declare(strict_types=1);

use App\Application\QuestionBank\Service\ProcessQuestionPdfImportAnalysisJobService;
use App\Application\QuestionBank\Service\QuestionPdfCandidateSegmenter;
use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
use App\Infrastructure\Database;
use App\Infrastructure\Extraction\PdfimagesQuestionPdfAssetExtractor;
use App\Infrastructure\Extraction\PdftotextPdfTextExtractor;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionDuplicateDetector;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionImportAnalysisRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfImportJobRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfImportCandidateCheckpointRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfQuestionWriter;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionQualitySignalRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionTaxonomyAssignmentRepository;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\DoctrineTaxonomySubjectRepository;
use App\Infrastructure\QuestionBank\ConfiguredQuestionImportAnalyzerFactory;

require __DIR__.'/../vendor/autoload.php';

$entityManager = DoctrineEntityManagerFactory::create();
$taxonomy = new DoctrineTaxonomySubjectRepository($entityManager);
$service = new ProcessQuestionPdfImportAnalysisJobService(
    new DoctrineQuestionPdfImportJobRepository($entityManager),
    new PdftotextPdfTextExtractor(),
    ConfiguredQuestionImportAnalyzerFactory::create(),
    $taxonomy,
    new DoctrineQuestionPdfQuestionWriter($entityManager, new DoctrineQuestionDuplicateDetector($entityManager), $taxonomy, new DoctrineQuestionTaxonomyAssignmentRepository($entityManager), new SubjectTaxonomyService(), new ImportedQuestionContentSanitizer()),
    new PdfimagesQuestionPdfAssetExtractor(),
    new QuestionPdfCandidateSegmenter(),
    new DoctrineQuestionImportAnalysisRepository($entityManager),
    new DoctrineQuestionQualitySignalRepository($entityManager),
    new DoctrineQuestionPdfImportCandidateCheckpointRepository($entityManager),
    max(0, (int) Database::env('QUESTION_IMPORT_MAX_CANDIDATES', '0')),
    max(1, (int) Database::env('QUESTION_IMPORT_BATCH_SIZE', '8')),
    max(10000, (int) Database::env('QUESTION_IMPORT_BATCH_PAYLOAD_BYTES', '120000')),
);
$count = 0;
while ($service->processNext()) { $entityManager->flush(); $entityManager->clear(); $count++; }
fwrite(STDOUT, "Processed {$count} question PDF analysis job(s)\n");
