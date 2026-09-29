<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_import_analyses')]
class QuestionImportAnalysisRecord
{
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'job_id', type: 'string', length: 36)] public string $jobId;
    #[ORM\Column(name: 'question_id', type: 'string', length: 36, nullable: true)] public ?string $questionId = null;
    #[ORM\Column(name: 'candidate_fingerprint', type: 'string', length: 64)] public string $candidateFingerprint;
    #[ORM\Column(name: 'schema_version', type: 'string', length: 64)] public string $schemaVersion;
    #[ORM\Column(name: 'algorithm_version', type: 'string', length: 64)] public string $algorithmVersion;
    #[ORM\Column(type: 'string', length: 64)] public string $provider;
    #[ORM\Column(type: 'string', length: 128, nullable: true)] public ?string $model = null;
    #[ORM\Column(name: 'evidence_pages', type: 'json')] public array $evidencePages = [];
    #[ORM\Column(type: 'json')] public array $metadata = [];
    #[ORM\Column(name: 'structure_type', type: 'string', length: 32)] public string $structureType;
    #[ORM\Column(name: 'answer_key_assessment', type: 'string', length: 32)] public string $answerKeyAssessment;
    #[ORM\Column(name: 'image_assessment', type: 'string', length: 32)] public string $imageAssessment;
    #[ORM\Column(name: 'input_tokens', type: 'integer', nullable: true)] public ?int $inputTokens = null;
    #[ORM\Column(name: 'output_tokens', type: 'integer', nullable: true)] public ?int $outputTokens = null;
    #[ORM\Column(name: 'total_tokens', type: 'integer', nullable: true)] public ?int $totalTokens = null;
    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 12, scale: 6, nullable: true)] public ?string $costUsd = null;
    #[ORM\Column(name: 'usage_availability', type: 'string', length: 16)] public string $usageAvailability;
    #[ORM\Column(name: 'duration_milliseconds', type: 'integer')] public int $durationMilliseconds;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
}
