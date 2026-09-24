<?php
declare(strict_types=1);

namespace App\Application\Catalog\DTO\Request;

final readonly class CreateSubjectRequestDto
{
    public function __construct(
        public string $syllabusId,
        public ?string $parentId,
        public string $name,
        public int $sortOrder,
        public ?string $sourceExcerpt = null,
        public ?int $sourcePage = null,
        public ?int $sourceStartOffset = null,
        public ?int $sourceEndOffset = null,
    ) {}
}
