<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Entity;
final readonly class Syllabus { public function __construct(public string $id, public string $examId, public string $name, public ?string $publishedAt, public ?string $sourceUrl, public ?string $documentPath = null, public ?string $documentSha256 = null, public ?string $documentOriginalName = null, public ?string $documentMimeType = null, public ?int $documentSize = null) {} }
