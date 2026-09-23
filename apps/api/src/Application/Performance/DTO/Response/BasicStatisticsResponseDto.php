<?php
declare(strict_types=1);
namespace App\Application\Performance\DTO\Response;
final readonly class BasicStatisticsResponseDto {
    /** @param array<string, array{total:int, correct:int, incorrect:int, percentage:float}> $subjects */
    public function __construct(public int $total,public int $correct,public int $incorrect,public float $percentage,public float $averageElapsedSeconds,public array $subjects) {}
}
