<?php
declare(strict_types=1);

namespace App\Application\Performance\DTO\Response;

final readonly class StudyMapSubjectResponseDto
{
    /** @param list<StudyMapSubjectResponseDto> $children */
    public function __construct(
        public string $id,
        public ?string $parentId,
        public string $name,
        public int $depth,
        public int $answered,
        public int $correct,
        public int $incorrect,
        public ?float $accuracy,
        public int $distinctDays,
        public string $evidenceStatus,
        public array $children,
    ) {}
}
