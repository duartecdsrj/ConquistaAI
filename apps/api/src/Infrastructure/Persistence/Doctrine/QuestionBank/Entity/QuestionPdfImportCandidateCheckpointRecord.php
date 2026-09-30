<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: "question_pdf_import_candidate_checkpoints")]
class QuestionPdfImportCandidateCheckpointRecord
{
    #[ORM\Id] #[ORM\Column(type: "string", length: 36)] public string $id;
    #[ORM\Column(name: "job_id", type: "string", length: 36)] public string $jobId;
    #[ORM\Column(name: "candidate_fingerprint", type: "string", length: 64)] public string $candidateFingerprint;
    #[ORM\Column(name: "position_index", type: "integer")] public int $positionIndex;
    #[ORM\Column(name: "candidate_payload", type: "json")] public array $candidatePayload = [];
    #[ORM\Column(type: "string", length: 16)] public string $status;
    #[ORM\Column(name: "retry_count", type: "integer")] public int $retryCount = 0;
    #[ORM\Column(name: "next_attempt_at", type: "datetime_immutable", nullable: true)] public ?DateTimeImmutable $nextAttemptAt = null;
    #[ORM\Column(name: "lease_started_at", type: "datetime_immutable", nullable: true)] public ?DateTimeImmutable $leaseStartedAt = null;
    #[ORM\Column(name: "error_message", type: "string", length: 500, nullable: true)] public ?string $errorMessage = null;
    #[ORM\Column(name: "created_at", type: "datetime_immutable")] public DateTimeImmutable $createdAt;
    #[ORM\Column(name: "updated_at", type: "datetime_immutable")] public DateTimeImmutable $updatedAt;
}
