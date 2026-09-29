<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Service;

use App\Application\Catalog\Port\PdfTextExtractorInterface;
use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalysisOutputDto;
use App\Application\QuestionBank\Port\QuestionImportAnalyzerInterface;
use App\Application\QuestionBank\Port\QuestionPdfAssetExtractorInterface;
use App\Application\QuestionBank\Port\QuestionPdfQuestionWriterInterface;
use App\Domain\QuestionBank\Entity\QuestionImportAnalysis;
use App\Domain\QuestionBank\Entity\QuestionPdfImportJob;
use App\Domain\QuestionBank\Entity\QuestionQualitySignal;
use App\Domain\QuestionBank\Enum\QuestionImportAnswerKeyAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportFindingCode;
use App\Domain\QuestionBank\Enum\QuestionImportImageAnchorStatus;
use App\Domain\QuestionBank\Enum\QuestionImportImageTarget;
use App\Domain\QuestionBank\Enum\QuestionQualityCategory;
use App\Domain\QuestionBank\Repository\QuestionImportAnalysisRepositoryInterface;
use App\Domain\QuestionBank\Repository\QuestionPdfImportJobRepositoryInterface;
use App\Domain\QuestionBank\Repository\QuestionQualitySignalRepositoryInterface;
use App\Domain\QuestionBank\ValueObject\QuestionImportFinding;
use App\Domain\QuestionBank\ValueObject\QuestionImportImageAnchor;
use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;

final class ProcessQuestionPdfImportAnalysisJobService
{
    private const MAX_RETRIES = 5;

    public function __construct(
        private readonly QuestionPdfImportJobRepositoryInterface $jobs,
        private readonly PdfTextExtractorInterface $pdf,
        private readonly QuestionImportAnalyzerInterface $analyzer,
        private readonly TaxonomySubjectRepositoryInterface $taxonomy,
        private readonly QuestionPdfQuestionWriterInterface $writer,
        private readonly QuestionPdfAssetExtractorInterface $assets,
        private readonly QuestionPdfCandidateSegmenter $segmenter,
        private readonly QuestionImportAnalysisRepositoryInterface $analyses,
        private readonly QuestionQualitySignalRepositoryInterface $signals,
        private readonly int $maxCandidatesPerJob = 0,
    ) {}

    public function processNext(): bool
    {
        $job = $this->jobs->claimNext();
        if ($job === null) return false;
        try {
            $pages = $this->pdf->extractPages($job->documentPath);
            $candidates = $this->segmenter->segment($pages, $this->assets->extract($job->documentPath, $job->documentSha256), $this->taxonomyPaths());
            $job = $this->state($job, 12, count($pages), count($candidates));
            $this->jobs->save($job);
            foreach ($candidates as $position => $candidate) {
                if ($this->jobs->isCancelled($job->id)) return true;
                if ($position < $job->processedChunks) continue;
                if ($this->maxCandidatesPerJob > 0 && $position >= $this->maxCandidatesPerJob) throw new \DomainException('Orçamento de análises do PDF atingido.');
                $response = $this->analyzer->analyze($candidate);
                $job = QuestionImportJobTelemetryAggregator::append($job, $response);
                if ($this->jobs->isCancelled($job->id)) return true;
                $analysis = $this->analysis($job, $candidate, $response->analysis, $response->provider, $response->model, $response->tokenUsage, $response->durationMilliseconds);
                $this->analyses->save($analysis);
                $result = $this->writer->write($job->createdBy, [$this->writerQuestion($candidate, $response->analysis)], $this->pageAssets($candidate), $job->id);
                $questionId = $result['questionIdsByCandidateFingerprint'][$candidate->candidateFingerprint] ?? null;
                if (is_string($questionId)) {
                    $this->analyses->linkQuestion($analysis->id, $questionId);
                    $this->persistSignal($analysis, $questionId);
                }
                $job = $this->state($job, min(98, 12 + (int) floor((($position + 1) / max(1, count($candidates))) * 86)), count($pages), count($candidates), $job->extractedQuestions + 1, $job->classifiedQuestions + $result['classified'], $job->createdQuestions + $result['created'], $job->duplicateQuestions + $result['duplicates'], $job->failedQuestions + $result['failed'], $job->createdTaxonomySubjects + $result['createdSubjects'], 'PROCESSING', null, $position + 1);
                $this->jobs->save($job);
            }
            $this->jobs->save($this->state($job, 100, count($pages), count($candidates), $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, 'COMPLETED', null, count($candidates)));
        } catch (\DomainException $exception) { $this->rescheduleOrFail($job, $exception); }
        catch (\Throwable $exception) { error_log('Question PDF analysis job '.$job->id.': '.$exception::class.' '.$exception->getMessage()); $this->jobs->save($this->state($job, $job->progress, $job->pageCount, $job->candidatePages, $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, 'FAILED', 'Não foi possível processar este PDF.')); }
        return true;
    }

    private function analysis(QuestionPdfImportJob $job, AnalyzeQuestionImportCandidateRequestDto $candidate, QuestionImportAnalysisOutputDto $output, string $provider, ?string $model, \App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage $usage, int $duration): QuestionImportAnalysis
    {
        $now = new \DateTimeImmutable('now');
        $findings = array_map(fn ($finding): QuestionImportFinding => new QuestionImportFinding($this->id(), $finding->code, $finding->severity, $finding->confidence, $finding->safeSummary, $finding->evidencePages), $output->findings);
        $anchors = array_map(fn ($anchor): QuestionImportImageAnchor => new QuestionImportImageAnchor($this->id(), $anchor->target, $anchor->optionLabel === null ? null : ord($anchor->optionLabel) - 64, $anchor->sourcePage, $anchor->sourceAssetIndex, $anchor->status), $output->imageAnchors);
        return new QuestionImportAnalysis($this->id(), $job->id, null, $candidate->candidateFingerprint, $output->schemaVersion, 'question-import-analysis-v1', $provider, $model, $output->evidencePages, $output->metadata, $output->structureType, $output->answerKeyAssessment, $output->imageAssessment, $usage, $duration, $findings, $anchors, $now);
    }

    /** @return array<string,mixed> */
    private function writerQuestion(AnalyzeQuestionImportCandidateRequestDto $candidate, QuestionImportAnalysisOutputDto $output): array
    {
        $options = array_map(static fn ($option): array => ['content' => $option->content], $output->options);
        $statementAssets = [];
        foreach ($output->imageAnchors as $anchor) if ($anchor->status === QuestionImportImageAnchorStatus::ANCHORED) {
            $asset = $this->candidateAsset($candidate, $anchor->sourcePage, $anchor->sourceAssetIndex);
            if ($asset === null) continue;
            if ($anchor->target === QuestionImportImageTarget::STATEMENT) $statementAssets[] = $asset;
            elseif ($anchor->optionLabel !== null) $options[ord($anchor->optionLabel) - 65]['verified_assets'][] = $asset;
        }
        return ['type' => 'MULTIPLE_CHOICE', 'source_candidate_fingerprint' => $candidate->candidateFingerprint, 'statement' => $output->statement, 'options' => $options, 'board' => $output->metadata->board, 'exam' => $output->metadata->exam, 'year' => $output->metadata->year, 'taxonomy_path' => $output->taxonomyPath, 'parent_subject' => $output->parentSubject, 'difficulty' => $output->difficulty, 'pages' => $output->evidencePages, 'verified_assets' => $statementAssets, 'correct_option' => $output->answerKeyAssessment === QuestionImportAnswerKeyAssessment::CONSISTENT ? $output->correctOptionLabel : null, 'answer_key_source' => 'AI_ESTIMATED'];
    }

    /** @return array<int,list<string>> */
    private function pageAssets(AnalyzeQuestionImportCandidateRequestDto $candidate): array
    {
        $assets = [];
        foreach ($candidate->images as $image) $assets[$image->pageNumber][$image->assetIndex] = $image->assetPath;
        return $assets;
    }

    /** @return array{path:string,page:int}|null */
    private function candidateAsset(AnalyzeQuestionImportCandidateRequestDto $candidate, int $page, ?int $index): ?array
    {
        foreach ($candidate->images as $image) if ($image->pageNumber === $page && $image->assetIndex === $index) return ['path' => $image->assetPath, 'page' => $page];
        return null;
    }

    private function persistSignal(QuestionImportAnalysis $analysis, string $questionId): void
    {
        foreach ($analysis->findings as $finding) {
            $category = match ($finding->code) { QuestionImportFindingCode::ANSWER_KEY_CONFLICT => QuestionQualityCategory::ANSWER_KEY_REVIEW, QuestionImportFindingCode::IMAGE_DISCREPANCY => QuestionQualityCategory::VISUAL_REVIEW, QuestionImportFindingCode::STRUCTURAL_INCONSISTENCY => QuestionQualityCategory::CONTENT_REVIEW, default => null };
            if ($category !== null) { $this->signals->replace(new QuestionQualitySignal($questionId, $analysis->id, $category, $finding->safeSummary, $analysis->createdAt)); return; }
        }
    }

    /** @return list<string> */
    private function taxonomyPaths(): array
    {
        $subjects = $this->taxonomy->list(0, 1000); $byId = []; foreach ($subjects as $subject) $byId[$subject->id] = $subject;
        $path = function ($subject) use (&$path, $byId): string { return ($subject->parentId !== null && isset($byId[$subject->parentId]) ? $path($byId[$subject->parentId]).' > ' : '').$subject->name; };
        $paths = array_map($path, $subjects); sort($paths); return array_slice($paths, 0, 700);
    }

    private function rescheduleOrFail(QuestionPdfImportJob $job, \DomainException $exception): void
    {
        error_log('Question PDF analysis job '.$job->id.': '.$exception->getMessage()); $attempt = $job->retryCount + 1;
        if ($attempt > self::MAX_RETRIES) { $this->jobs->save($this->state($job, $job->progress, $job->pageCount, $job->candidatePages, $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, 'FAILED', 'O analisador de importação permaneceu indisponível após novas tentativas.')); return; }
        $wait = min(15, 2 ** ($attempt - 1)); $this->jobs->save($this->state($job, $job->progress, $job->pageCount, $job->candidatePages, $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, 'PENDING', 'Aguardando nova tentativa do analisador de importação.', null, $job->processedChunks, $attempt, (new \DateTimeImmutable('now'))->modify('+'.$wait.' minutes')));
    }

    private function state(QuestionPdfImportJob $job, int $progress, int $pages, int $candidates, int $extracted = 0, int $classified = 0, int $created = 0, int $duplicates = 0, int $failed = 0, int $newSubjects = 0, string $status = 'PROCESSING', ?string $error = null, ?int $processedChunks = null, ?int $retryCount = null, ?\DateTimeImmutable $nextAttemptAt = null): QuestionPdfImportJob { return new QuestionPdfImportJob($job->id, $job->syllabusId, $job->createdBy, $job->documentPath, $job->documentSha256, $job->documentOriginalName, $status, $progress, $pages, $candidates, $processedChunks ?? $job->processedChunks, $retryCount ?? $job->retryCount, $nextAttemptAt, $extracted, $classified, $created, $duplicates, $failed, $newSubjects, $error, $job->createdAt, $job->startedAt, $status === 'COMPLETED' || $status === 'FAILED' ? new \DateTimeImmutable('now') : null, $job->analysisTelemetry); }
    private function id(): string { $bytes = random_bytes(16); $bytes[6] = chr((ord($bytes[6]) & 15) | 64); $bytes[8] = chr((ord($bytes[8]) & 63) | 128); return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4)); }
}
