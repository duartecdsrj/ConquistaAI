<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Repository;

/**
 * Detects changes that can alter a published-question audit without exposing
 * persistence details to the application service.
 */
interface PublishedQuestionAuditChangeDetectorInterface
{
    public function hasAuditRelevantChangesSince(\DateTimeImmutable $since): bool;
}
