<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Response;

final readonly class QuestionPdfImportJobTelemetryResponseDto
{
    public function __construct(
        public string $provider,
        public ?string $model,
        public string $schemaVersion,
        public int $durationMilliseconds,
        public string $usageAvailability,
        public ?int $inputTokens,
        public ?int $outputTokens,
        public ?int $totalTokens,
        public ?string $costUsd,
        public int $analysisCount,
        public int $answerKeyConflictCount,
        public int $imageDiscrepancyCount,
        public int $structuralIssueCount,
    ) {}
}
