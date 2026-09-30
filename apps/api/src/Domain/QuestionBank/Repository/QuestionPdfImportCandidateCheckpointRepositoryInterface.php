<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Repository;
use App\Domain\QuestionBank\Entity\QuestionPdfImportCandidateCheckpoint;
interface QuestionPdfImportCandidateCheckpointRepositoryInterface
{
    public function save(QuestionPdfImportCandidateCheckpoint $checkpoint): void;
    public function claimNext(): ?QuestionPdfImportCandidateCheckpoint;
    /**  list<QuestionPdfImportCandidateCheckpoint> */
    public function claimCompatible(string $jobId, int $afterPosition, int $limit, int $maxPayloadBytes): array;
    /** @param array{classified:int,created:int,duplicates:int,failed:int,created_subjects:int} $outcome */
    public function complete(QuestionPdfImportCandidateCheckpoint $checkpoint, array $outcome): void;
    public function retry(QuestionPdfImportCandidateCheckpoint $checkpoint, string $safeMessage, \DateTimeImmutable $nextAttemptAt): void;
    public function fail(QuestionPdfImportCandidateCheckpoint $checkpoint, string $safeMessage): void;
    public function hasOpenForJob(string $jobId): bool;
    public function summaryForJob(string $jobId): \App\Domain\QuestionBank\Entity\QuestionPdfImportCandidateCheckpointSummary;
}
