<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ValueObject;

final readonly class QuestionImportJobTelemetry
{
    public function __construct(
        public ?string $provider,
        public ?string $model,
        public ?string $schemaVersion,
        public int $durationMilliseconds,
        public QuestionImportTokenUsage $tokenUsage,
        public int $analysisCount,
        public int $answerKeyConflictCount,
        public int $imageDiscrepancyCount,
        public int $structuralIssueCount,
    ) {
        foreach ([$durationMilliseconds, $analysisCount, $answerKeyConflictCount, $imageDiscrepancyCount, $structuralIssueCount] as $value) if ($value < 0) throw new \InvalidArgumentException('Telemetria de importação inválida.');
    }
}
