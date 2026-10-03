<?php
declare(strict_types=1);

namespace App\Domain\Performance\Entity;

use App\Domain\Performance\Enum\StudyScheduleStatus;

final readonly class StudyMapScheduleItem
{
    /** @param list<string> $predecessorSubjectIds */
    public function __construct(
        public string $userId,
        public string $examId,
        public string $taxonomySubjectId,
        public \DateTimeImmutable $startDate,
        public \DateTimeImmutable $endDate,
        public StudyScheduleStatus $status,
        public ?\DateTimeImmutable $completedAt,
        public array $predecessorSubjectIds,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
        public ?int $estimatedMinutes = null,
    ) {
        if ($endDate < $startDate) {
            throw new \InvalidArgumentException('A data final não pode ser anterior à inicial.');
        }
    }
}
