<?php
declare(strict_types=1);
namespace App\Domain\Review\Entity;
use App\Domain\Review\Enum\MasteryConfidence;
final class UserConceptMastery { public function __construct(public readonly string $userId,public readonly string $taxonomySubjectId,public ?float $masteryScore,public MasteryConfidence $confidence,public int $evidenceCount,public ?\DateTimeImmutable $lastEvidenceAt,public array $signals,public \DateTimeImmutable $updatedAt){} }
