<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Mapper;

use App\Application\QuestionBank\DTO\Response\QuestionPdfImportJobResponseDto;
use App\Application\QuestionBank\DTO\Response\QuestionPdfImportJobTelemetryResponseDto;
use App\Domain\QuestionBank\Entity\QuestionPdfImportJob;

final class QuestionPdfImportJobResponseMapper
{
    public function map(QuestionPdfImportJob $job): QuestionPdfImportJobResponseDto
    {
        $telemetry = $job->analysisTelemetry;
        return new QuestionPdfImportJobResponseDto(
            $job->id, $job->syllabusId, $job->documentOriginalName, $job->status,
            $job->progress, $job->pageCount, $job->candidatePages,
            $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions,
            $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects,
            $job->errorMessage, $job->createdAt->format(DATE_ATOM),
            $job->startedAt?->format(DATE_ATOM), $job->finishedAt?->format(DATE_ATOM),
            $telemetry === null ? null : new QuestionPdfImportJobTelemetryResponseDto(
                $telemetry->provider, $telemetry->model, $telemetry->schemaVersion,
                $telemetry->durationMilliseconds, $telemetry->tokenUsage->availability->value,
                $telemetry->tokenUsage->inputTokens, $telemetry->tokenUsage->outputTokens,
                $telemetry->tokenUsage->totalTokens, $telemetry->tokenUsage->costUsd,
                $telemetry->analysisCount, $telemetry->answerKeyConflictCount,
                $telemetry->imageDiscrepancyCount, $telemetry->structuralIssueCount,
            ),
        );
    }
}
