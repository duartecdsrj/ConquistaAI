<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Service;

use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzerResponseDto;
use App\Domain\QuestionBank\Entity\QuestionPdfImportJob;
use App\Domain\QuestionBank\Enum\QuestionImportFindingCode;
use App\Domain\QuestionBank\Enum\QuestionImportUsageAvailability;
use App\Domain\QuestionBank\ValueObject\QuestionImportJobTelemetry;
use App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage;

final class QuestionImportJobTelemetryAggregator
{
    public static function append(QuestionPdfImportJob $job, QuestionImportAnalyzerResponseDto $response): QuestionPdfImportJob
    {
        $previous = $job->analysisTelemetry;
        $usage = self::usage($previous?->tokenUsage, $response->tokenUsage);
        $findings = $response->analysis->findings;
        $conflicts = 0; $images = 0; $structural = 0;
        foreach ($findings as $finding) match ($finding->code) {
            QuestionImportFindingCode::ANSWER_KEY_CONFLICT => $conflicts++,
            QuestionImportFindingCode::IMAGE_DISCREPANCY => $images++,
            QuestionImportFindingCode::STRUCTURAL_INCONSISTENCY => $structural++,
            default => null,
        };
        $telemetry = new QuestionImportJobTelemetry($response->provider, $response->model, $response->analysis->schemaVersion, ($previous?->durationMilliseconds ?? 0) + $response->durationMilliseconds, $usage, ($previous?->analysisCount ?? 0) + 1, ($previous?->answerKeyConflictCount ?? 0) + $conflicts, ($previous?->imageDiscrepancyCount ?? 0) + $images, ($previous?->structuralIssueCount ?? 0) + $structural);
        return new QuestionPdfImportJob($job->id, $job->syllabusId, $job->createdBy, $job->documentPath, $job->documentSha256, $job->documentOriginalName, $job->status, $job->progress, $job->pageCount, $job->candidatePages, $job->processedChunks, $job->retryCount, $job->nextAttemptAt, $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, $job->errorMessage, $job->createdAt, $job->startedAt, $job->finishedAt, $telemetry);
    }

    /** @param list<\App\Domain\QuestionBank\Entity\QuestionImportAnalysis> $analyses */
    public static function fromAnalyses(array $analyses): ?QuestionImportJobTelemetry
    {
        if ($analyses === []) return null;
        $provider = $analyses[0]->provider; $model = $analyses[0]->model; $schema = $analyses[0]->schemaVersion; $duration = 0; $usage = null; $conflicts = 0; $images = 0; $structural = 0;
        foreach ($analyses as $analysis) {
            if ($analysis->provider !== $provider) $provider = "multiple";
            if ($analysis->model !== $model) $model = null;
            if ($analysis->schemaVersion !== $schema) $schema = null;
            $duration += $analysis->durationMilliseconds;
            $usage = self::usage($usage, $analysis->tokenUsage);
            foreach ($analysis->findings as $finding) match ($finding->code) {
                QuestionImportFindingCode::ANSWER_KEY_CONFLICT => $conflicts++,
                QuestionImportFindingCode::IMAGE_DISCREPANCY => $images++,
                QuestionImportFindingCode::STRUCTURAL_INCONSISTENCY => $structural++,
                default => null,
            };
        }
        return new QuestionImportJobTelemetry($provider, $model, $schema, $duration, $usage ?? QuestionImportTokenUsage::unavailable(), count($analyses), $conflicts, $images, $structural);
    }

    private static function usage(?QuestionImportTokenUsage $previous, QuestionImportTokenUsage $current): QuestionImportTokenUsage
    {
        if (($previous !== null && $previous->availability === QuestionImportUsageAvailability::UNAVAILABLE) || $current->availability === QuestionImportUsageAvailability::UNAVAILABLE) return QuestionImportTokenUsage::unavailable();
        $cost = $current->costUsd === null ? null : ($previous?->costUsd === null ? $current->costUsd : number_format((float) $previous->costUsd + (float) $current->costUsd, 6, '.', ''));
        return new QuestionImportTokenUsage(QuestionImportUsageAvailability::AVAILABLE, ($previous?->inputTokens ?? 0) + $current->inputTokens, ($previous?->outputTokens ?? 0) + $current->outputTokens, ($previous?->totalTokens ?? 0) + $current->totalTokens, $cost);
    }
}
