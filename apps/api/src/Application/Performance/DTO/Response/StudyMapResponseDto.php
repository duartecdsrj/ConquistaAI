<?php
declare(strict_types=1);

namespace App\Application\Performance\DTO\Response;

final readonly class StudyMapResponseDto
{
    /** @param list<StudyMapSubjectResponseDto> $subjects @param list<StudyMapScheduleResponseDto> $schedule */
    public function __construct(
        public string $examId,
        public ?string $from,
        public ?string $to,
        public int $answered,
        public int $correct,
        public int $incorrect,
        public ?float $accuracy,
        public int $unclassifiedAnswers,
        public array $subjects,
        public array $schedule,
    ) {}
}
