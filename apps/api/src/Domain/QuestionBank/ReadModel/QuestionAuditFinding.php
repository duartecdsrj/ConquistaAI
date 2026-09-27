<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\ReadModel;
final readonly class QuestionAuditFinding {
    public function __construct(
        public string $id,
        public string $questionId,
        public ?string $sourcePdfJobId,
        public ?int $sourcePage,
        public string $code,
        public string $confidence,
        public string $status,
        public string $message,
        public string $createdAt,
        public ?array $structureAfter = null,
    ) {}
}
