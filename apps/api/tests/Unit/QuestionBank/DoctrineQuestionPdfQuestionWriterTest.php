<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionBank;

use App\Domain\QuestionBank\Repository\QuestionDuplicateDetectorInterface;
use App\Domain\QuestionBank\Repository\QuestionTaxonomyAssignmentRepositoryInterface;
use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use App\Domain\Taxonomy\Entity\TaxonomySubject;
use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfQuestionWriter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class DoctrineQuestionPdfQuestionWriterTest extends TestCase
{
    public function testRejectsAnAggregateSubjectThatHasChildren(): void
    {
        $taxonomy = $this->createMock(TaxonomySubjectRepositoryInterface::class);
        $aggregate = new TaxonomySubject('ti', null, 'Tecnologia da Informação', 'tecnologia-da-informacao', null, 0, true);
        $taxonomy->expects(self::once())->method('findBySlug')->with('tecnologia-da-informacao')->willReturn($aggregate);
        $taxonomy->expects(self::once())->method('hasChildren')->with('ti')->willReturn(true);
        $writer = new DoctrineQuestionPdfQuestionWriter(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(QuestionDuplicateDetectorInterface::class),
            $taxonomy,
            $this->createMock(QuestionTaxonomyAssignmentRepositoryInterface::class),
            new SubjectTaxonomyService(),
            new ImportedQuestionContentSanitizer(),
        );
        $method = new \ReflectionMethod(DoctrineQuestionPdfQuestionWriter::class, 'specificTaxonomy');
        self::assertNull($method->invoke($writer, ['taxonomy_path' => ['Tecnologia da Informação']]));
    }
}
