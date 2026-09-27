<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_audit_runs')]
class QuestionAuditRunRecord
{
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'algorithm_version', type: 'string', length: 64)] public string $algorithmVersion;
    #[ORM\Column(type: 'string', length: 16)] public string $status;
    #[ORM\Column(type: 'string', length: 32)] public string $scope;
    #[ORM\Column(type: 'json')] public array $summary = [];
    #[ORM\Column(name: 'error_message', type: 'string', length: 500, nullable: true)] public ?string $errorMessage = null;
    #[ORM\Column(name: 'started_at', type: 'datetime_immutable', nullable: true)] public ?\DateTimeImmutable $startedAt = null;
    #[ORM\Column(name: 'finished_at', type: 'datetime_immutable', nullable: true)] public ?\DateTimeImmutable $finishedAt = null;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public \DateTimeImmutable $createdAt;
}
