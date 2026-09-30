<?php
declare(strict_types=1);

namespace Tests\Integration\QuestionBank;

use App\Domain\QuestionBank\Entity\QuestionPdfImportCandidateCheckpoint;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfImportCandidateCheckpointRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfImportJobRepository;
use PHPUnit\Framework\TestCase;

final class DoctrineQuestionPdfImportCheckpointRepositoryTest extends TestCase
{
    public function testRetriesOnlyTheClaimedCandidateAndConsolidatesTerminalCounts(): void
    {
        $em = DoctrineEntityManagerFactory::create();
        $db = $em->getConnection();
        $jobId = $this->uuid();
        $db->beginTransaction();
        try {
            $db->executeStatement("SET FOREIGN_KEY_CHECKS=0");
            $this->insertJob($db, $jobId);
            $repository = new DoctrineQuestionPdfImportCandidateCheckpointRepository($em);
            $now = new \DateTimeImmutable("now");
            $repository->save(new QuestionPdfImportCandidateCheckpoint($this->uuid(), $jobId, str_repeat("a", 64), 0, ["schema_version" => "v1"], "PENDING", 0, null, null, null, $now, $now));
            $repository->save(new QuestionPdfImportCandidateCheckpoint($this->uuid(), $jobId, str_repeat("a", 64), 0, ["schema_version" => "v1"], "PENDING", 0, null, null, null, $now, $now));
            $repository->save(new QuestionPdfImportCandidateCheckpoint($this->uuid(), $jobId, str_repeat("b", 64), 1, ["schema_version" => "v1"], "PENDING", 0, null, null, null, $now, $now));
            $em->flush();
            $first = $repository->claimNext();
            self::assertNotNull($first);
            $repository->fail($first, "Dados inválidos.");
            $second = $repository->claimNext();
            self::assertNotNull($second);
            $repository->complete($second, ["classified" => 1, "created" => 1, "duplicates" => 0, "failed" => 0, "created_subjects" => 0]);
            $summary = $repository->summaryForJob($jobId);
            self::assertSame(2, $summary->total);
            self::assertGreaterThanOrEqual(1, $summary->failed);
        } finally {
            $db->executeStatement("SET FOREIGN_KEY_CHECKS=1");
            $db->rollBack();
            $em->clear();
        }
    }

    public function testJobKeepsItsOriginalStartAcrossARecoverableClaim(): void
    {
        $em = DoctrineEntityManagerFactory::create();
        $db = $em->getConnection();
        $jobId = $this->uuid();
        $db->beginTransaction();
        try {
            $db->executeStatement("SET FOREIGN_KEY_CHECKS=0");
            $this->insertJob($db, $jobId);
            $repository = new DoctrineQuestionPdfImportJobRepository($em);
            $first = $repository->claimNext();
            self::assertNotNull($first);
            self::assertNotNull($first->startedAt);
            $db->executeStatement("UPDATE question_pdf_import_jobs SET status = :status, next_attempt_at = NULL WHERE id = :id", ["status" => "PENDING", "id" => $jobId]);
            $em->clear();
            $second = $repository->claimNext();
            self::assertNotNull($second);
            self::assertSame($first->startedAt->format("Y-m-d H:i:s"), $second->startedAt->format("Y-m-d H:i:s"));
        } finally {
            $db->executeStatement("SET FOREIGN_KEY_CHECKS=1");
            $db->rollBack();
            $em->clear();
        }
    }

    private function insertJob(\Doctrine\DBAL\Connection $db, string $jobId): void
    {
        $db->insert("question_pdf_import_jobs", ["id" => $jobId, "created_by" => $this->uuid(), "document_path" => "/tmp/import.pdf", "document_sha256" => str_repeat("c", 64), "document_original_name" => "import.pdf", "status" => "PENDING", "progress" => 0, "page_count" => 0, "candidate_pages" => 0, "processed_chunks" => 0, "retry_count" => 0, "extracted_questions" => 0, "classified_questions" => 0, "created_questions" => 0, "duplicate_questions" => 0, "failed_questions" => 0, "created_taxonomy_subjects" => 0, "created_at" => (new \DateTimeImmutable("now"))->format("Y-m-d H:i:s")]);
    }

    private function uuid(): string
    {
        return sprintf("%s-%s-4000-8000-%s", substr(bin2hex(random_bytes(4)), 0, 8), substr(bin2hex(random_bytes(2)), 0, 4), substr(bin2hex(random_bytes(6)), 0, 12));
    }
}
