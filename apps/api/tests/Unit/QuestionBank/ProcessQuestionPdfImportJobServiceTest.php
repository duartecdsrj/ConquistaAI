<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionBank;

use App\Application\Assistant\Port\AssistantProviderInterface;
use App\Application\Catalog\Port\PdfTextExtractorInterface;
use App\Application\QuestionBank\Port\QuestionPdfAssetExtractorInterface;
use App\Application\QuestionBank\Port\QuestionPdfQuestionWriterInterface;
use App\Application\QuestionBank\Service\ProcessQuestionPdfImportJobService;
use App\Domain\QuestionBank\Repository\QuestionPdfImportJobRepositoryInterface;
use App\Domain\Taxonomy\Entity\TaxonomySubject;
use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class ProcessQuestionPdfImportJobServiceTest extends TestCase
{
    public function testBuildsCanonicalPathsForTheClassifier(): void
    {
        $service = new ProcessQuestionPdfImportJobService(
            $this->createMock(QuestionPdfImportJobRepositoryInterface::class),
            $this->createMock(PdfTextExtractorInterface::class),
            $this->createMock(AssistantProviderInterface::class),
            $this->createMock(TaxonomySubjectRepositoryInterface::class),
            $this->createMock(QuestionPdfQuestionWriterInterface::class),
            $this->createMock(QuestionPdfAssetExtractorInterface::class),
        );
        $method = new \ReflectionMethod(ProcessQuestionPdfImportJobService::class, 'taxonomyOutline');
        $outline = $method->invoke($service, [
            new TaxonomySubject('root', null, 'Tecnologia da Informação', 'tecnologia-da-informacao', null, 0, true),
            new TaxonomySubject('data', 'root', 'Dados e Inteligência Artificial', 'dados-e-ia', null, 1, true),
            new TaxonomySubject('open', 'data', 'Dados Abertos', 'dados-abertos', null, 2, true),
        ]);
        self::assertStringContainsString('Tecnologia da Informação > Dados e Inteligência Artificial > Dados Abertos', $outline);
    }
}
