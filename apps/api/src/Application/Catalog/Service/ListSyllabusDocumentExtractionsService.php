<?php
declare(strict_types=1);
namespace App\Application\Catalog\Service;
use App\Application\Catalog\DTO\Response\SyllabusDocumentExtractionResponseDto;use App\Application\Catalog\Mapper\SyllabusDocumentExtractionResponseMapper;use App\Domain\Catalog\Repository\SyllabusDocumentExtractionRepositoryInterface;
final class ListSyllabusDocumentExtractionsService { public function __construct(private readonly SyllabusDocumentExtractionRepositoryInterface $extractions,private readonly SyllabusDocumentExtractionResponseMapper $mapper){} /** @return list<SyllabusDocumentExtractionResponseDto> */ public function list(string $syllabusId):array{return array_map($this->mapper->map(...),$this->extractions->listForSyllabus($syllabusId));} }
