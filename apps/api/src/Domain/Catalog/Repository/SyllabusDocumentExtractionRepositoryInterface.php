<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Repository;
use App\Domain\Catalog\Entity\SyllabusDocumentExtraction;
interface SyllabusDocumentExtractionRepositoryInterface { /** @param list<SyllabusDocumentExtraction> $extractions */ public function replaceForDocument(string $documentSha256,array $extractions):void; /** @return list<SyllabusDocumentExtraction> */ public function listForSyllabus(string $syllabusId):array; }
