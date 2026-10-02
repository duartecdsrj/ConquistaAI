<?php
declare(strict_types=1);

namespace App\Application\Review\Service;

use App\Application\Review\Port\TransactionManagerInterface;
use App\Domain\Review\Entity\ReviewSession;

final readonly class NavigateReviewSessionService
{
    public function __construct(
        private BuildReviewSessionService $sessions,
        private TransactionManagerInterface $transactions,
    ) {}

    public function execute(string $userId, string $sessionId, string $direction, \DateTimeImmutable $now): ReviewSession
    {
        return $this->transactions->transactional(
            fn (): ReviewSession => $this->sessions->navigate($userId, $sessionId, $direction, $now),
        );
    }
}
