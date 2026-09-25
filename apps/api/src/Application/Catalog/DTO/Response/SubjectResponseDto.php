<?php
declare(strict_types=1);

namespace App\Application\Catalog\DTO\Response;

final readonly class SubjectResponseDto
{
    public function __construct(
        public string $id,
        public string $syllabusId,
        public ?string $parentId,
        public string $name,
        public int $sortOrder,
        public ?string $sourceExcerpt,
        public ?int $sourcePage,
        public ?int $sourceStartOffset,
        public ?int $sourceEndOffset,
        public float $selectionWeight,
        public string $weightSource,
        public float $weightConfidence,
        public ?string $weightCalculatedAt,
    ) {}
}
