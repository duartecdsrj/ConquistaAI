<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Entity;

final readonly class QuestionPdfImportCandidateCheckpointSummary
{
    public function __construct(
        public int $total, public int $pending, public int $processing, public int $completed, public int $failed, public int $classified, public int $created, public int $duplicates, public int $createdSubjects, public int $outcomeFailed,
    ) {}

    public function hasOpenWork(): bool
    {
        return $this->pending > 0 || $this->processing > 0;
    }
}
