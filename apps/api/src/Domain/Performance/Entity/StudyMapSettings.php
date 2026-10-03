<?php
declare(strict_types=1);

namespace App\Domain\Performance\Entity;

final readonly class StudyMapSettings
{
    public function __construct(
        public string $userId,
        public string $examId,
        public ?\DateTimeImmutable $examDate,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
    ) {}
}
