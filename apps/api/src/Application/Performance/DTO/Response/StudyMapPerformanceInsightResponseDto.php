<?php
declare(strict_types=1);

namespace App\Application\Performance\DTO\Response;

final readonly class StudyMapPerformanceInsightResponseDto
{
    public function __construct(public string $subjectId, public string $name, public int $answered, public ?float $accuracy, public string $evidenceStatus) {}
}
