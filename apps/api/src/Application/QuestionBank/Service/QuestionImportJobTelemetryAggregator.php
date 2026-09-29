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

    private static function usage(?QuestionImportTokenUsage $previous, QuestionImportTokenUsage $current): QuestionImportTokenUsage
    {
        if (($previous !== null && $previous->availability === QuestionImportUsageAvailability::UNAVAILABLE) || $current->availability === QuestionImportUsageAvailability::UNAVAILABLE) return QuestionImportTokenUsage::unavailable();
        $cost = $current->costUsd === null ? null : ($previous?->costUsd === null ? $current->costUsd : number_format((float) $previous->costUsd + (float) $current->costUsd, 6, '.', ''));
        return new QuestionImportTokenUsage(QuestionImportUsageAvailability::AVAILABLE, ($previous?->inputTokens ?? 0) + $current->inputTokens, ($previous?->outputTokens ?? 0) + $current->outputTokens, ($previous?->totalTokens ?? 0) + $current->totalTokens, $cost);
    }
}
