<?php
declare(strict_types=1);

namespace App\Application\Performance\DTO\Response;

final readonly class StudyMapPlanningSummaryResponseDto
{
    public function __construct(
        public ?int $estimatedMinutes,
        public ?int $completedMinutes,
        public ?int $remainingMinutes,
        public ?float $overallProgress,
        public ?string $examDate,
        public ?int $daysUntilExam,
    ) {}
}
