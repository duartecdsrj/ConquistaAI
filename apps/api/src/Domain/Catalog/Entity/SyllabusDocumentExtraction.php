<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Entity;
final readonly class SyllabusDocumentExtraction { public function __construct(public string $id,public string $syllabusId,public string $documentSha256,public int $pageNumber,public string $textContent,public int $startOffset,public int $endOffset){} }
