<?php
declare(strict_types=1);

namespace App\Domain\Performance\Repository;

use App\Domain\Performance\ValueObject\CompletedAnswer;

interface PerformanceStatisticsRepositoryInterface
{
    /** @return list<CompletedAnswer> */
    public function completedAnswersForUser(string $userId): array;
}
