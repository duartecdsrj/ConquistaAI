<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Response;
final readonly class SyllabusDocumentExtractionResponseDto { public function __construct(public string $documentSha256,public int $pageNumber,public string $textContent,public int $startOffset,public int $endOffset){} }
