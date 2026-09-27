<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_audit_findings')]
class QuestionAuditFindingRecord
{
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'audit_run_id', type: 'string', length: 36)] public string $auditRunId;
    #[ORM\Column(name: 'question_id', type: 'string', length: 36)] public string $questionId;
    #[ORM\Column(name: 'source_pdf_job_id', type: 'string', length: 36, nullable: true)] public ?string $sourcePdfJobId = null;
    #[ORM\Column(name: 'source_page', type: 'integer', nullable: true)] public ?int $sourcePage = null;
    #[ORM\Column(type: 'string', length: 64)] public string $code;
    #[ORM\Column(type: 'string', length: 16)] public string $confidence;
    #[ORM\Column(type: 'string', length: 32)] public string $status;
    #[ORM\Column(type: 'string', length: 500)] public string $message;
    #[ORM\Column(name: 'structure_before', type: 'json', nullable: true)] public ?array $structureBefore = null;
    #[ORM\Column(name: 'structure_after', type: 'json', nullable: true)] public ?array $structureAfter = null;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public \DateTimeImmutable $createdAt;
}
