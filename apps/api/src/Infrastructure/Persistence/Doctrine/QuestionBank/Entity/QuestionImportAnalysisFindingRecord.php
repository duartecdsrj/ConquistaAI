<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_import_analysis_findings')]
class QuestionImportAnalysisFindingRecord
{
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'analysis_id', type: 'string', length: 36)] public string $analysisId;
    #[ORM\Column(type: 'string', length: 64)] public string $code;
    #[ORM\Column(type: 'string', length: 16)] public string $severity;
    #[ORM\Column(type: 'decimal', precision: 5, scale: 4, nullable: true)] public ?string $confidence = null;
    #[ORM\Column(name: 'safe_summary', type: 'string', length: 500)] public string $safeSummary;
    #[ORM\Column(name: 'evidence_pages', type: 'json')] public array $evidencePages = [];
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
}
