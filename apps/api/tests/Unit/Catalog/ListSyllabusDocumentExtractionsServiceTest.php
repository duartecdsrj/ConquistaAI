<?php
declare(strict_types=1);

namespace Tests\Unit\Catalog;

use App\Application\Catalog\Mapper\SyllabusDocumentExtractionResponseMapper;
use App\Application\Catalog\Service\ListSyllabusDocumentExtractionsService;
use App\Domain\Catalog\Entity\SyllabusDocumentExtraction;
use App\Domain\Catalog\Repository\SyllabusDocumentExtractionRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class ListSyllabusDocumentExtractionsServiceTest extends TestCase
{
    public function testMapsPagesWithTheirProvenance(): void
    {
        $repository = new class implements SyllabusDocumentExtractionRepositoryInterface { public function replaceForDocument(string $documentSha256, array $extractions): void {} public function listForSyllabus(string $syllabusId): array { return [new SyllabusDocumentExtraction('extraction', $syllabusId, str_repeat('a', 64), 2, 'Conteúdo', 12, 21)]; } };
        $result = (new ListSyllabusDocumentExtractionsService($repository, new SyllabusDocumentExtractionResponseMapper()))->list('syllabus');
        self::assertSame(2, $result[0]->pageNumber); self::assertSame('Conteúdo', $result[0]->textContent); self::assertSame(12, $result[0]->startOffset); self::assertSame(21, $result[0]->endOffset);
    }
}
