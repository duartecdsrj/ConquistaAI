<?php
declare(strict_types=1);

namespace App\Application\Performance\Service;

use App\Application\Performance\DTO\Response\BasicStatisticsResponseDto;
use App\Domain\Performance\Repository\PerformanceStatisticsRepositoryInterface;

final class GetBasicStatisticsService
{
    public function __construct(
        private readonly PerformanceStatisticsRepositoryInterface $statistics,
        private readonly BasicStatisticsService $calculator,
    ) {
    }

    public function getForUser(string $userId): BasicStatisticsResponseDto
    {
        return $this->calculator->calculate($this->statistics->completedAnswersForUser($userId));
    }
}
