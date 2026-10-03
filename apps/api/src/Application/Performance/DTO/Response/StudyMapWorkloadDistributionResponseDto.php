<?php
declare(strict_types=1);

namespace App\Application\Performance\DTO\Response;

final readonly class StudyMapWorkloadDistributionResponseDto
{
    public function __construct(public string $subjectId, public string $name, public int $estimatedMinutes, public float $percentage) {}
}
