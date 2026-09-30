<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Entity;
final readonly class QuestionPdfImportCandidateCheckpoint
{
    public function __construct(public string $id, public string $jobId, public string $candidateFingerprint, public int $positionIndex, public array $candidatePayload, public string $status, public int $retryCount, public ?\DateTimeImmutable $nextAttemptAt, public ?\DateTimeImmutable $leaseStartedAt, public ?string $errorMessage, public \DateTimeImmutable $createdAt, public \DateTimeImmutable $updatedAt) {}
}
