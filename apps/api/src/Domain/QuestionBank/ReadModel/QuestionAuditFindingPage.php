<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\ReadModel;
final readonly class QuestionAuditFindingPage {
    /** @param list<QuestionAuditFinding> $items */
    public function __construct(
        public array $items,
        public int $page,
        public int $perPage,
        public int $total,
    ) {}
}
