<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Domain\QuestionBank\Entity\QuestionPdfImportCandidateCheckpoint;
use App\Domain\QuestionBank\Entity\QuestionPdfImportCandidateCheckpointSummary;
use App\Domain\QuestionBank\Repository\QuestionPdfImportCandidateCheckpointRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionPdfImportCandidateCheckpointRecord;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

final class DoctrineQuestionPdfImportCandidateCheckpointRepository implements QuestionPdfImportCandidateCheckpointRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}
    public function save(QuestionPdfImportCandidateCheckpoint $checkpoint): void
    {
        $record = $this->findRecord($checkpoint->id, $checkpoint->jobId, $checkpoint->candidateFingerprint);
        if ($record instanceof QuestionPdfImportCandidateCheckpointRecord && ((int) ($record->candidatePayload['_outcome']['created'] ?? 0) > 0 || (int) ($record->candidatePayload['_outcome']['duplicates'] ?? 0) > 0)) return;
        if (!$record instanceof QuestionPdfImportCandidateCheckpointRecord) {
            $record = new QuestionPdfImportCandidateCheckpointRecord();
            $record->id = $checkpoint->id;
            $this->em->persist($record);
        }
        foreach (["jobId", "candidateFingerprint", "positionIndex", "candidatePayload", "status", "retryCount", "nextAttemptAt", "leaseStartedAt", "errorMessage", "createdAt", "updatedAt"] as $property) $record->$property = $checkpoint->$property;
    }
    public function claimNext(): ?QuestionPdfImportCandidateCheckpoint
    {
        try { return $this->em->wrapInTransaction(function (): ?QuestionPdfImportCandidateCheckpoint {
            $now = new \DateTimeImmutable("now"); $stale = $now->modify("-10 minutes");
            $record = $this->nextPending($now) ?? $this->nextExpiredLease($stale);
            if (!$record instanceof QuestionPdfImportCandidateCheckpointRecord) return null;
            $record->status = "PROCESSING"; $record->leaseStartedAt = $now; $record->nextAttemptAt = null; $record->errorMessage = null; $record->updatedAt = $now;
            $this->em->flush(); return $this->map($record);
        }); } catch (\Doctrine\DBAL\Exception) { return null; }
    }
    /**  list<QuestionPdfImportCandidateCheckpoint> */
    public function claimCompatible(string $jobId, int $afterPosition, int $limit, int $maxPayloadBytes): array
    {
        try { return $this->em->wrapInTransaction(function () use ($jobId, $afterPosition, $limit, $maxPayloadBytes): array {
            $now = new \DateTimeImmutable("now"); $records = $this->em->createQueryBuilder()->select("checkpoint")->from(QuestionPdfImportCandidateCheckpointRecord::class, "checkpoint")->where("checkpoint.jobId = :job")->andWhere("checkpoint.positionIndex > :after")->andWhere("checkpoint.status = :pending")->andWhere("checkpoint.nextAttemptAt IS NULL OR checkpoint.nextAttemptAt <= :now")->setParameter("job", $jobId)->setParameter("after", $afterPosition)->setParameter("pending", "PENDING")->setParameter("now", $now)->orderBy("checkpoint.positionIndex", "ASC")->setMaxResults(max(0, $limit))->getQuery()->setLockMode(LockMode::PESSIMISTIC_WRITE)->getResult();
            $claimed = []; $bytes = 0; foreach ($records as $record) { if (!$record instanceof QuestionPdfImportCandidateCheckpointRecord) continue; $size = strlen(json_encode($record->candidatePayload, JSON_THROW_ON_ERROR)); if ($claimed !== [] && $bytes + $size > $maxPayloadBytes) break; $bytes += $size; $record->status = "PROCESSING"; $record->leaseStartedAt = $now; $record->nextAttemptAt = null; $record->errorMessage = null; $record->updatedAt = $now; $claimed[] = $this->map($record); } $this->em->flush(); return $claimed;
        }); } catch (\Doctrine\DBAL\Exception\DeadlockException) { return []; }
    }

    public function complete(QuestionPdfImportCandidateCheckpoint $checkpoint, array $outcome): void { $this->transition($checkpoint, "COMPLETED", null, null, $outcome); }
    public function retry(QuestionPdfImportCandidateCheckpoint $checkpoint, string $safeMessage, \DateTimeImmutable $nextAttemptAt): void { $this->transition($checkpoint, "PENDING", $safeMessage, $nextAttemptAt, null, $checkpoint->retryCount + 1); }
    public function fail(QuestionPdfImportCandidateCheckpoint $checkpoint, string $safeMessage): void { $this->transition($checkpoint, "FAILED", $safeMessage, null, ["classified" => 0, "created" => 0, "duplicates" => 0, "failed" => 1, "created_subjects" => 0]); }
    public function hasOpenForJob(string $jobId): bool { return $this->summaryForJob($jobId)->hasOpenWork(); }
    public function summaryForJob(string $jobId): QuestionPdfImportCandidateCheckpointSummary
    {
        $records = $this->em->createQueryBuilder()->select("checkpoint")->from(QuestionPdfImportCandidateCheckpointRecord::class, "checkpoint")->where("checkpoint.jobId = :job")->setParameter("job", $jobId)->getQuery()->getResult();
        $counts = ["total" => 0, "pending" => 0, "processing" => 0, "completed" => 0, "failed" => 0, "classified" => 0, "created" => 0, "duplicates" => 0, "created_subjects" => 0, "outcome_failed" => 0];
        foreach ($records as $record) { if (!$record instanceof QuestionPdfImportCandidateCheckpointRecord) continue; $counts["total"]++; $counts[strtolower($record->status)]++; $outcome = $record->candidatePayload["_outcome"] ?? []; foreach (["classified", "created", "duplicates", "created_subjects"] as $key) $counts[$key] += (int) ($outcome[$key] ?? 0); if (array_key_exists("failed", $outcome)) $counts["outcome_failed"] += (int) $outcome["failed"]; elseif ($record->status === "FAILED") $counts["outcome_failed"]++; }
        return new QuestionPdfImportCandidateCheckpointSummary($counts["total"], $counts["pending"], $counts["processing"], $counts["completed"], $counts["failed"], $counts["classified"], $counts["created"], $counts["duplicates"], $counts["created_subjects"], $counts["outcome_failed"]);
    }
    private function transition(QuestionPdfImportCandidateCheckpoint $checkpoint, string $status, ?string $message, ?\DateTimeImmutable $nextAttemptAt, ?array $outcome, ?int $retryCount = null): void
    {
        $this->em->wrapInTransaction(function () use ($checkpoint, $status, $message, $nextAttemptAt, $outcome, $retryCount): void {
            $record = $this->em->createQueryBuilder()->select("checkpoint")->from(QuestionPdfImportCandidateCheckpointRecord::class, "checkpoint")->where("checkpoint.id = :id")->andWhere("checkpoint.status = :processing")->setParameter("id", $checkpoint->id)->setParameter("processing", "PROCESSING")->getQuery()->setLockMode(LockMode::PESSIMISTIC_WRITE)->setHint(Query::HINT_REFRESH, true)->getOneOrNullResult();
            if (!$record instanceof QuestionPdfImportCandidateCheckpointRecord) return;
            $record->status = $status; $record->errorMessage = $message; $record->nextAttemptAt = $nextAttemptAt; $record->retryCount = $retryCount ?? $record->retryCount; $record->leaseStartedAt = $status === "PENDING" ? null : $record->leaseStartedAt; $record->updatedAt = new \DateTimeImmutable("now");
            if ($outcome !== null) $record->candidatePayload["_outcome"] = $outcome;
            $this->em->flush();
        });
    }
    private function findRecord(string $id, string $jobId, string $fingerprint): ?QuestionPdfImportCandidateCheckpointRecord
    {
        $record = $this->em->find(QuestionPdfImportCandidateCheckpointRecord::class, $id);
        if ($record instanceof QuestionPdfImportCandidateCheckpointRecord) return $record;
        foreach ($this->em->getUnitOfWork()->getScheduledEntityInsertions() as $scheduled) {
            if ($scheduled instanceof QuestionPdfImportCandidateCheckpointRecord && $scheduled->jobId === $jobId && $scheduled->candidateFingerprint === $fingerprint) return $scheduled;
        }
        $record = $this->em->createQueryBuilder()->select("checkpoint")->from(QuestionPdfImportCandidateCheckpointRecord::class, "checkpoint")->where("checkpoint.jobId = :job")->andWhere("checkpoint.candidateFingerprint = :fingerprint")->setParameter("job", $jobId)->setParameter("fingerprint", $fingerprint)->getQuery()->getOneOrNullResult();
        return $record instanceof QuestionPdfImportCandidateCheckpointRecord ? $record : null;
    }
    private function nextPending(\DateTimeImmutable $now): ?QuestionPdfImportCandidateCheckpointRecord
    {
        $record = $this->em->createQueryBuilder()->select("checkpoint")->from(QuestionPdfImportCandidateCheckpointRecord::class, "checkpoint")->where("checkpoint.status = :pending")->andWhere("checkpoint.nextAttemptAt IS NULL OR checkpoint.nextAttemptAt <= :now")->setParameter("pending", "PENDING")->setParameter("now", $now)->orderBy("checkpoint.createdAt", "ASC")->setMaxResults(1)->getQuery()->setLockMode(LockMode::PESSIMISTIC_WRITE)->getOneOrNullResult();
        return $record instanceof QuestionPdfImportCandidateCheckpointRecord ? $record : null;
    }
    private function nextExpiredLease(\DateTimeImmutable $stale): ?QuestionPdfImportCandidateCheckpointRecord
    {
        $record = $this->em->createQueryBuilder()->select("checkpoint")->from(QuestionPdfImportCandidateCheckpointRecord::class, "checkpoint")->where("checkpoint.status = :processing")->andWhere("checkpoint.leaseStartedAt <= :stale")->setParameter("processing", "PROCESSING")->setParameter("stale", $stale)->orderBy("checkpoint.createdAt", "ASC")->setMaxResults(1)->getQuery()->setLockMode(LockMode::PESSIMISTIC_WRITE)->getOneOrNullResult();
        return $record instanceof QuestionPdfImportCandidateCheckpointRecord ? $record : null;
    }
    private function map(QuestionPdfImportCandidateCheckpointRecord $record): QuestionPdfImportCandidateCheckpoint { return new QuestionPdfImportCandidateCheckpoint($record->id, $record->jobId, $record->candidateFingerprint, $record->positionIndex, $record->candidatePayload, $record->status, $record->retryCount, $record->nextAttemptAt, $record->leaseStartedAt, $record->errorMessage, $record->createdAt, $record->updatedAt); }
}
