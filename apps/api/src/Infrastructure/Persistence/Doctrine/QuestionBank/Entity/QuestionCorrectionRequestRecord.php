<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: 'question_correction_requests')]
class QuestionCorrectionRequestRecord {
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'question_id', type: 'string', length: 36)] public string $questionId;
    #[ORM\Column(name: 'requested_by', type: 'string', length: 36)] public string $requestedBy;
    #[ORM\Column(type: 'text')] public string $instruction;
    #[ORM\Column(type: 'string', length: 16)] public string $status;
    #[ORM\Column(name: 'original_snapshot', type: 'json')] public array $originalSnapshot;
    #[ORM\Column(type: 'json', nullable: true)] public ?array $proposal = null;
    #[ORM\Column(name: 'error_message', type: 'string', length: 500, nullable: true)] public ?string $errorMessage = null;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public \DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'started_at', type: 'datetime_immutable', nullable: true)] public ?\DateTimeImmutable $startedAt = null;
    #[ORM\Column(name: 'finished_at', type: 'datetime_immutable', nullable: true)] public ?\DateTimeImmutable $finishedAt = null;
    #[ORM\Column(name: 'approved_by', type: 'string', length: 36, nullable: true)] public ?string $approvedBy = null;
    #[ORM\Column(name: 'approved_at', type: 'datetime_immutable', nullable: true)] public ?\DateTimeImmutable $approvedAt = null;
}
