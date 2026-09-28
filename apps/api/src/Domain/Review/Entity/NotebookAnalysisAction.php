<?php
declare(strict_types=1);
namespace App\Domain\Review\Entity;
use App\Domain\Review\Enum\NotebookAnalysisActionType;
final readonly class NotebookAnalysisAction { public function __construct(public string $id,public string $executionId,public ?string $questionId,public ?string $taxonomySubjectId,public ?string $flashcardId,public NotebookAnalysisActionType $type,public string $reason,public float $confidence,public array $payload,public ?\DateTimeImmutable $appliedAt,public \DateTimeImmutable $createdAt){if($confidence<0||$confidence>1)throw new \InvalidArgumentException('Confiança inválida.');} }
