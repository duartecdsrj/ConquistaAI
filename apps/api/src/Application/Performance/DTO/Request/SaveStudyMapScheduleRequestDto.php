<?php
declare(strict_types=1);

namespace App\Application\Performance\DTO\Request;

use App\Domain\Performance\Enum\StudyScheduleStatus;

final readonly class SaveStudyMapScheduleRequestDto
{
    /** @param list<string> $predecessorSubjectIds */
    public function __construct(
        public string $userId,
        public string $examId,
        public string $subjectId,
        public \DateTimeImmutable $startDate,
        public \DateTimeImmutable $endDate,
        public StudyScheduleStatus $status,
        public array $predecessorSubjectIds,
        public ?int $estimatedMinutes = null,
    ) {}
}
