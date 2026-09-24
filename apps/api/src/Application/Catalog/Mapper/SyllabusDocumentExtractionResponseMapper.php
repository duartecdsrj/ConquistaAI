<?php
declare(strict_types=1);
namespace App\Application\Catalog\Mapper;
use App\Application\Catalog\DTO\Response\SyllabusDocumentExtractionResponseDto;use App\Domain\Catalog\Entity\SyllabusDocumentExtraction;
final class SyllabusDocumentExtractionResponseMapper { public function map(SyllabusDocumentExtraction $item):SyllabusDocumentExtractionResponseDto{return new SyllabusDocumentExtractionResponseDto($item->documentSha256,$item->pageNumber,$item->textContent,$item->startOffset,$item->endOffset);} }
