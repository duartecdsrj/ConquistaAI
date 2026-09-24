<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Request;
final readonly class UploadSyllabusDocumentRequestDto { public function __construct(public string $syllabusId,public string $originalName,public string $mimeType,public string $contents){} }
