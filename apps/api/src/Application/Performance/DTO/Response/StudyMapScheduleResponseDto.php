<?php
declare(strict_types=1);

namespace App\Application\Performance\DTO\Response;

final readonly class StudyMapScheduleResponseDto
{
    /** @param list<string> $predecessorSubjectIds */
    public function __construct(
        public string $subjectId,
        public string $startDate,
        public string $endDate,
        public string $status,
        public ?string $completedAt,
        public array $predecessorSubjectIds,
        public ?int $estimatedMinutes,
    ) {}
}
