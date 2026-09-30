<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Service;

use App\Application\Catalog\Port\PdfTextExtractorInterface;
use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Request\QuestionImportEvidencePageDto;
use App\Application\QuestionBank\DTO\Request\QuestionImportImageManifestItemDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalysisOutputDto;
use App\Application\QuestionBank\Port\QuestionImportAnalyzerInterface;
use App\Application\QuestionBank\Port\QuestionPdfAssetExtractorInterface;
use App\Application\QuestionBank\Port\QuestionPdfQuestionWriterInterface;
use App\Domain\QuestionBank\Entity\QuestionImportAnalysis;
use App\Domain\QuestionBank\Entity\QuestionPdfImportJob;
use App\Domain\QuestionBank\Entity\QuestionPdfImportCandidateCheckpoint;
use App\Domain\QuestionBank\Entity\QuestionQualitySignal;
use App\Domain\QuestionBank\Enum\QuestionImportAnswerKeyAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportFindingCode;
use App\Domain\QuestionBank\Enum\QuestionImportImageAnchorStatus;
use App\Domain\QuestionBank\Enum\QuestionImportImageTarget;
use App\Domain\QuestionBank\Enum\QuestionQualityCategory;
use App\Domain\QuestionBank\Repository\QuestionImportAnalysisRepositoryInterface;
use App\Domain\QuestionBank\Repository\QuestionPdfImportJobRepositoryInterface;
use App\Domain\QuestionBank\Repository\QuestionPdfImportCandidateCheckpointRepositoryInterface;
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
        private readonly QuestionPdfImportCandidateCheckpointRepositoryInterface $checkpoints,
        private readonly int $maxCandidatesPerJob = 0,
        private readonly int $batchSize = 8,
        private readonly int $batchPayloadBytes = 120000,
    ) {}

    public function processNext(): bool
    {
        $checkpoint = $this->checkpoints->claimNext();
        if ($checkpoint !== null) {
            $batch = array_merge([$checkpoint], $this->checkpoints->claimCompatible($checkpoint->jobId, $checkpoint->positionIndex, max(0, $this->batchSize - 1), $this->batchPayloadBytes));
            return $this->processCheckpointBatch($batch);
        }
        $job = $this->jobs->claimNext();
        if ($job === null) { foreach ($this->jobs->listProcessing() as $processing) if (!$this->checkpoints->hasOpenForJob($processing->id)) { $this->consolidateJob($processing->id); return true; } return false; }
        try {
            $pages = $this->pdf->extractPages($job->documentPath);
            $taxonomyPaths = $this->taxonomyPaths();
            $candidates = $this->segmenter->segment($pages, [], $taxonomyPaths);
            $visualPages = [];
            foreach ($candidates as $candidate) foreach ($candidate->evidencePages as $evidence) if ($this->requiresVisualExtraction($evidence->content)) $visualPages[] = $evidence->pageNumber;
            if ($visualPages !== []) $candidates = $this->segmenter->segment($pages, $this->assets->extract($job->documentPath, $job->documentSha256, $visualPages), $taxonomyPaths);
            $job = $this->state($job, 12, count($pages), count($candidates), $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, "PROCESSING", null, $job->processedChunks);
            $this->jobs->save($job);
            $this->materializeCheckpoints($job, $candidates);
            if ($candidates === []) $this->jobs->save($this->state($job, 100, count($pages), 0, 0, 0, 0, 0, 0, 0, "COMPLETED", null, 0));
            return true;
        } catch (\DomainException $exception) { $this->rescheduleOrFail($job, $exception); }
        catch (\Throwable $exception) { error_log('Question PDF analysis job '.$job->id.': '.$exception::class.' '.$exception->getMessage()); $this->jobs->save($this->state($job, $job->progress, $job->pageCount, $job->candidatePages, $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, 'FAILED', 'Não foi possível processar este PDF.')); }
        return true;
    }

    /**  list<AnalyzeQuestionImportCandidateRequestDto> $candidates */
    private function materializeCheckpoints(QuestionPdfImportJob $job, array $candidates): void
    {
        $now = new \DateTimeImmutable("now");
        foreach ($candidates as $position => $candidate) {
            $payload = ["schema_version" => $candidate->schemaVersion, "evidence_pages" => array_map(static fn ($page): array => ["page_number" => $page->pageNumber, "content" => $page->content], $candidate->evidencePages), "images" => array_map(static fn ($image): array => ["page_number" => $image->pageNumber, "asset_index" => $image->assetIndex, "asset_path" => $image->assetPath], $candidate->images), "taxonomy_paths" => $candidate->taxonomyPaths];
            $this->checkpoints->save(new QuestionPdfImportCandidateCheckpoint($this->id(), $job->id, $candidate->candidateFingerprint, $position, $payload, "PENDING", 0, null, null, null, $now, $now));
        }
    }

    /**  list<QuestionPdfImportCandidateCheckpoint> $checkpoints */
    private function processCheckpointBatch(array $checkpoints): bool
    {
        $job = $this->jobs->findById($checkpoints[0]->jobId);
        if ($job === null || $this->jobs->isCancelled($checkpoints[0]->jobId)) { foreach ($checkpoints as $checkpoint) $this->checkpoints->complete($checkpoint, ["classified" => 0, "created" => 0, "duplicates" => 0, "failed" => 0, "created_subjects" => 0]); return true; }
        $candidates = []; foreach ($checkpoints as $checkpoint) $candidates[$checkpoint->candidateFingerprint] = $this->candidateFromCheckpoint($checkpoint);
        try { $batch = $this->analyzer->analyzeBatch(array_values($candidates)); } catch (\DomainException $exception) { error_log("Question PDF batch ".$job->id.": ".$exception->getMessage()); foreach ($checkpoints as $checkpoint) $this->checkpoints->fail($checkpoint, "A análise deste candidato retornou dados inválidos."); $this->consolidateJob($job->id); return true; } catch (\Throwable $exception) { foreach ($checkpoints as $checkpoint) $this->retryCheckpoint($checkpoint, $exception); $this->consolidateJob($job->id); return true; }
        foreach ($checkpoints as $checkpoint) {
            $response = $batch->responses[$checkpoint->candidateFingerprint] ?? null;
            if ($response === null) { if (in_array($checkpoint->candidateFingerprint, $batch->invalidCandidateFingerprints, true)) $this->checkpoints->fail($checkpoint, "A análise deste candidato retornou dados inválidos."); else $this->retryCheckpoint($checkpoint, new \RuntimeException("Resposta ausente no lote.")); continue; }
            try { $candidate = $candidates[$checkpoint->candidateFingerprint]; $analysis = $this->analysis($job, $candidate, $response->analysis, $response->provider, $response->model, $response->tokenUsage, $response->durationMilliseconds); $this->analyses->save($analysis); $result = $this->writer->write($job->createdBy, [$this->writerQuestion($candidate, $response->analysis)], $this->pageAssets($candidate), $job->id); $questionId = $result["questionIdsByCandidateFingerprint"][$candidate->candidateFingerprint] ?? null; if (is_string($questionId)) { $this->analyses->linkQuestion($analysis->id, $questionId); $this->persistSignal($analysis, $questionId); } $this->checkpoints->complete($checkpoint, ["classified" => $result["classified"], "created" => $result["created"], "duplicates" => $result["duplicates"], "failed" => $result["failed"], "created_subjects" => $result["createdSubjects"]]); } catch (\DomainException $exception) { $this->checkpoints->fail($checkpoint, "A análise deste candidato retornou dados inválidos."); } catch (\Throwable $exception) { $this->retryCheckpoint($checkpoint, $exception); }
        }
        $this->consolidateJob($job->id); return true;
    }

    private function retryCheckpoint(QuestionPdfImportCandidateCheckpoint $checkpoint, \Throwable $exception): void
    {
        error_log("Question PDF candidate ".$checkpoint->candidateFingerprint.": ".$exception::class." ".$exception->getMessage());
        if ($checkpoint->retryCount + 1 > self::MAX_RETRIES) { $this->checkpoints->fail($checkpoint, "O analisador permaneceu indisponível após novas tentativas."); return; }
        $minutes = min(15, 2 ** $checkpoint->retryCount); $this->checkpoints->retry($checkpoint, "Aguardando nova tentativa do analisador de importação.", (new \DateTimeImmutable("now"))->modify("+".$minutes." minutes"));
    }

    private function processCheckpoint(QuestionPdfImportCandidateCheckpoint $checkpoint): bool
    {
        $job = $this->jobs->findById($checkpoint->jobId);
        if ($job === null || $this->jobs->isCancelled($checkpoint->jobId)) {
            $this->checkpoints->complete($checkpoint, ["classified" => 0, "created" => 0, "duplicates" => 0, "failed" => 0, "created_subjects" => 0]);
            if ($job !== null) $this->consolidateJob($job->id);
            return true;
        }
        try {
            $candidate = $this->candidateFromCheckpoint($checkpoint);
            $response = $this->analyzer->analyze($candidate);
            $analysis = $this->analysis($job, $candidate, $response->analysis, $response->provider, $response->model, $response->tokenUsage, $response->durationMilliseconds);
            $this->analyses->save($analysis);
            $result = $this->writer->write($job->createdBy, [$this->writerQuestion($candidate, $response->analysis)], $this->pageAssets($candidate), $job->id);
            $questionId = $result["questionIdsByCandidateFingerprint"][$candidate->candidateFingerprint] ?? null;
            if (is_string($questionId)) {
                $this->analyses->linkQuestion($analysis->id, $questionId);
                $this->persistSignal($analysis, $questionId);
            }
            $this->checkpoints->complete($checkpoint, ["classified" => $result["classified"], "created" => $result["created"], "duplicates" => $result["duplicates"], "failed" => $result["failed"], "created_subjects" => $result["createdSubjects"]]);
        } catch (\DomainException $exception) {
            error_log("Question PDF candidate ".$checkpoint->candidateFingerprint.": ".$exception->getMessage());
            $this->checkpoints->fail($checkpoint, "A análise deste candidato retornou dados inválidos.");
        } catch (\Throwable $exception) {
            error_log("Question PDF candidate ".$checkpoint->candidateFingerprint.": ".$exception::class." ".$exception->getMessage());
            if ($checkpoint->retryCount + 1 > self::MAX_RETRIES) {
                $this->checkpoints->fail($checkpoint, "O analisador permaneceu indisponível após novas tentativas.");
            } else {
                $minutes = min(15, 2 ** $checkpoint->retryCount);
                $this->checkpoints->retry($checkpoint, "Aguardando nova tentativa do analisador de importação.", (new \DateTimeImmutable("now"))->modify("+".$minutes." minutes"));
            }
        }
        $this->consolidateJob($checkpoint->jobId);
        return true;
    }

    private function consolidateJob(string $jobId): void
    {
        $job = $this->jobs->findById($jobId);
        if ($job === null || $this->jobs->isCancelled($jobId)) return;
        $summary = $this->checkpoints->summaryForJob($jobId);
        if ($summary->total === 0) return;
        $terminal = $summary->completed + $summary->failed;
        $progress = $summary->hasOpenWork() ? min(98, 12 + (int) floor(($terminal / $summary->total) * 86)) : 100;
        $status = $summary->hasOpenWork() ? "PROCESSING" : ($summary->completed === 0 ? "FAILED" : "COMPLETED");
        $error = $status === "FAILED" ? "Nenhum candidato do PDF pôde ser processado." : null;
        $telemetry = QuestionImportJobTelemetryAggregator::fromAnalyses($this->analyses->listForJob($jobId));
        if ($telemetry !== null) $job = new QuestionPdfImportJob($job->id, $job->syllabusId, $job->createdBy, $job->documentPath, $job->documentSha256, $job->documentOriginalName, $job->status, $job->progress, $job->pageCount, $job->candidatePages, $job->processedChunks, $job->retryCount, $job->nextAttemptAt, $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, $job->errorMessage, $job->createdAt, $job->startedAt, $job->finishedAt, $telemetry);
        $this->jobs->save($this->state($job, $progress, $job->pageCount, $summary->total, $terminal, $summary->classified, $summary->created, $summary->duplicates, $summary->outcomeFailed, $summary->createdSubjects, $status, $error, $terminal));
    }

    private function candidateFromCheckpoint(\App\Domain\QuestionBank\Entity\QuestionPdfImportCandidateCheckpoint $checkpoint): AnalyzeQuestionImportCandidateRequestDto
    {
        $payload = $checkpoint->candidatePayload;
        $pages = array_map(static fn (array $page): QuestionImportEvidencePageDto => new QuestionImportEvidencePageDto((int) $page["page_number"], (string) $page["content"]), (array) ($payload["evidence_pages"] ?? []));
        $images = array_map(static fn (array $image): QuestionImportImageManifestItemDto => new QuestionImportImageManifestItemDto((int) $image["page_number"], (int) $image["asset_index"], (string) $image["asset_path"]), (array) ($payload["images"] ?? []));
        return new AnalyzeQuestionImportCandidateRequestDto($checkpoint->candidateFingerprint, $pages, $images, array_values((array) ($payload["taxonomy_paths"] ?? [])), (string) ($payload["schema_version"] ?? "question-import-analysis-v1"));
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
        $markerNumber = 0;
        foreach ($output->imageAnchors as $anchor) if ($anchor->status === QuestionImportImageAnchorStatus::ANCHORED) {
            $asset = $this->candidateAsset($candidate, $anchor->sourcePage, $anchor->sourceAssetIndex);
            if ($asset === null) continue;
            $marker = sprintf("[[FIGURA:%d]]", ++$markerNumber);
            if ($anchor->target === QuestionImportImageTarget::STATEMENT && substr_count($output->statement, $marker) === 1) $statementAssets[] = $asset;
            elseif ($anchor->optionLabel !== null && substr_count($options[ord($anchor->optionLabel) - 65]["content"], $marker) === 1) $options[ord($anchor->optionLabel) - 65]["verified_assets"][] = $asset;
        }
        $parent = count($output->taxonomyPath) > 1 ? $output->taxonomyPath[array_key_last($output->taxonomyPath) - 1] : null;
        return ['type' => 'MULTIPLE_CHOICE', 'source_candidate_fingerprint' => $candidate->candidateFingerprint, 'statement' => $output->statement, 'options' => $options, 'board' => $output->metadata->board, 'exam' => $this->sourceMetadata($output->metadata->exam, $output->metadata->position), 'year' => $output->metadata->year, 'taxonomy_path' => $output->taxonomyPath, 'parent_subject' => $parent, 'difficulty' => $output->difficulty, 'pages' => $output->evidencePages, 'verified_assets' => $statementAssets, 'correct_option' => $output->answerKeyAssessment === QuestionImportAnswerKeyAssessment::CONSISTENT ? $output->correctOptionLabel : null, 'answer_key_source' => $this->officialAnswerKeySource($candidate, $output->correctOptionLabel)];
    }

    /** @return array<int,list<string>> */
    private function sourceMetadata(?string $exam, ?string $position): ?string
    {
        $parts = array_values(array_filter([$exam, $position], static fn (?string $value): bool => $value !== null && trim($value) !== ""));
        return $parts === [] ? null : implode("/", $parts);
    }

    private function officialAnswerKeySource(AnalyzeQuestionImportCandidateRequestDto $candidate, ?string $label): string
    {
        if ($label === null) return "AI_ESTIMATED";
        $label = mb_strtoupper(trim($label));
        if (preg_match("/^[A-E]$/", $label) !== 1) return "AI_ESTIMATED";
        foreach ($candidate->evidencePages as $page) {
            if (preg_match("/\bgabarito\b\s*(?:[:\-]\s*)?(?:letra\s*)?" . preg_quote($label, "/") . "\b/iu", $page->content) === 1) return "OFFICIAL";
        }
        return "AI_ESTIMATED";
    }

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

    private function requiresVisualExtraction(string $content): bool
    {
        return preg_match('/\\b(?:figura|imagem|gr[aá]fico|tabela|quadro|diagrama|esquema|ilustra[cç][aã]o|mapa|fluxograma|exibid[oa]s\\s+(?:a\\s+)?seguir|conforme\\s+(?:o\\s+)?c[oó]digo|c[oó]digo\\s+abaixo)\\b/iu', $content) === 1
            || preg_match('/(?:^|\\n)\\s*[a-e]\\)\\s*(?:\\n\\s*){2,}(?=(?:[a-e]\\)|resolu[cç][aã]o:|gabarito:))/iu', $content) === 1;
    }

    /** @return list<string> */
    private function taxonomyPaths(): array
    {
        $subjects = $this->taxonomy->list(0, 1000); $byId = []; $parentIds = []; foreach ($subjects as $subject) { $byId[$subject->id] = $subject; if ($subject->parentId !== null) $parentIds[$subject->parentId] = true; }
        $path = function ($subject) use (&$path, $byId): string { return ($subject->parentId !== null && isset($byId[$subject->parentId]) ? $path($byId[$subject->parentId]).' > ' : '').$subject->name; };
        $paths = array_map($path, array_values(array_filter($subjects, static fn ($subject): bool => !isset($parentIds[$subject->id])))); sort($paths); return array_slice($paths, 0, 700);
    }

    private function rescheduleOrFail(QuestionPdfImportJob $job, \DomainException $exception): void
    {
        error_log('Question PDF analysis job '.$job->id.': '.$exception->getMessage()); $attempt = $job->retryCount + 1;
        if ($attempt > self::MAX_RETRIES) { $this->jobs->save($this->state($job, $job->progress, $job->pageCount, $job->candidatePages, $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, 'FAILED', 'O analisador de importação permaneceu indisponível após novas tentativas.')); return; }
        $wait = min(15, 2 ** ($attempt - 1)); $this->jobs->save($this->state($job, $job->progress, $job->pageCount, $job->candidatePages, $job->extractedQuestions, $job->classifiedQuestions, $job->createdQuestions, $job->duplicateQuestions, $job->failedQuestions, $job->createdTaxonomySubjects, 'PENDING', 'Aguardando nova tentativa do analisador de importação.', $job->processedChunks, $attempt, (new \DateTimeImmutable('now'))->modify('+'.$wait.' minutes')));
    }

    private function state(QuestionPdfImportJob $job, int $progress, int $pages, int $candidates, int $extracted = 0, int $classified = 0, int $created = 0, int $duplicates = 0, int $failed = 0, int $newSubjects = 0, string $status = 'PROCESSING', ?string $error = null, ?int $processedChunks = null, ?int $retryCount = null, ?\DateTimeImmutable $nextAttemptAt = null): QuestionPdfImportJob { return new QuestionPdfImportJob($job->id, $job->syllabusId, $job->createdBy, $job->documentPath, $job->documentSha256, $job->documentOriginalName, $status, $progress, $pages, $candidates, $processedChunks ?? $job->processedChunks, $retryCount ?? $job->retryCount, $nextAttemptAt, $extracted, $classified, $created, $duplicates, $failed, $newSubjects, $error, $job->createdAt, $status === 'PROCESSING' ? ($job->startedAt ?? new \DateTimeImmutable('now')) : $job->startedAt, $status === 'COMPLETED' || $status === 'FAILED' ? new \DateTimeImmutable('now') : null, $job->analysisTelemetry); }
    private function id(): string { $bytes = random_bytes(16); $bytes[6] = chr((ord($bytes[6]) & 15) | 64); $bytes[8] = chr((ord($bytes[8]) & 63) | 128); return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4)); }
}
