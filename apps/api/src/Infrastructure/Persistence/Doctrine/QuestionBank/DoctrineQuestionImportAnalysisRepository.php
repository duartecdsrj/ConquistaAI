<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Domain\QuestionBank\Entity\QuestionImportAnalysis;
use App\Domain\QuestionBank\Enum\QuestionImportAnswerKeyAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportFindingCode;
use App\Domain\QuestionBank\Enum\QuestionImportFindingSeverity;
use App\Domain\QuestionBank\Enum\QuestionImportImageAnchorStatus;
use App\Domain\QuestionBank\Enum\QuestionImportImageAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportImageTarget;
use App\Domain\QuestionBank\Enum\QuestionImportStructureType;
use App\Domain\QuestionBank\Enum\QuestionImportUsageAvailability;
use App\Domain\QuestionBank\Repository\QuestionImportAnalysisRepositoryInterface;
use App\Domain\QuestionBank\ValueObject\QuestionImportFinding;
use App\Domain\QuestionBank\ValueObject\QuestionImportImageAnchor;
use App\Domain\QuestionBank\ValueObject\QuestionImportMetadata;
use App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionImportAnalysisFindingRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionImportAnalysisRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionImportImageAnchorRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineQuestionImportAnalysisRepository implements QuestionImportAnalysisRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function save(QuestionImportAnalysis $analysis): void
    {
        if ($this->entityManager->find(QuestionImportAnalysisRecord::class, $analysis->id) instanceof QuestionImportAnalysisRecord) throw new \DomainException('Análise de importação já persistida.');
        $record = new QuestionImportAnalysisRecord();
        $record->id = $analysis->id; $record->jobId = $analysis->jobId; $record->questionId = $analysis->questionId; $record->candidateFingerprint = $analysis->candidateFingerprint; $record->schemaVersion = $analysis->schemaVersion; $record->algorithmVersion = $analysis->algorithmVersion; $record->provider = $analysis->provider; $record->model = $analysis->model; $record->evidencePages = $analysis->evidencePages;
        $record->metadata = ['exam' => $analysis->metadata->exam, 'position' => $analysis->metadata->position, 'board' => $analysis->metadata->board, 'year' => $analysis->metadata->year, 'evidence_pages' => $analysis->metadata->evidencePages];
        $record->structureType = $analysis->structureType->value; $record->answerKeyAssessment = $analysis->answerKeyAssessment->value; $record->imageAssessment = $analysis->imageAssessment->value; $record->inputTokens = $analysis->tokenUsage->inputTokens; $record->outputTokens = $analysis->tokenUsage->outputTokens; $record->totalTokens = $analysis->tokenUsage->totalTokens; $record->costUsd = $analysis->tokenUsage->costUsd; $record->usageAvailability = $analysis->tokenUsage->availability->value; $record->durationMilliseconds = $analysis->durationMilliseconds; $record->createdAt = $analysis->createdAt;
        $this->entityManager->persist($record);
        foreach ($analysis->findings as $finding) $this->persistFinding($analysis, $finding);
        foreach ($analysis->imageAnchors as $anchor) $this->persistAnchor($analysis, $anchor);
    }

    public function linkQuestion(string $analysisId, string $questionId): void
    {
        $record = $this->entityManager->find(QuestionImportAnalysisRecord::class, $analysisId);
        if (!$record instanceof QuestionImportAnalysisRecord) throw new \DomainException('Análise de importação não encontrada.');
        if ($record->questionId !== null && $record->questionId !== $questionId) throw new \DomainException('Análise de importação já vinculada.');
        $record->questionId = $questionId;
    }

    public function listForJob(string $jobId): array
    {
        $records = $this->entityManager->createQueryBuilder()->select('analysis')->from(QuestionImportAnalysisRecord::class, 'analysis')->where('analysis.jobId = :jobId')->setParameter('jobId', $jobId)->orderBy('analysis.createdAt', 'ASC')->getQuery()->getResult();
        return array_map($this->map(...), $records);
    }

    private function persistFinding(QuestionImportAnalysis $analysis, QuestionImportFinding $finding): void
    {
        $record = new QuestionImportAnalysisFindingRecord(); $record->id = $finding->id; $record->analysisId = $analysis->id; $record->code = $finding->code->value; $record->severity = $finding->severity->value; $record->confidence = $finding->confidence === null ? null : number_format($finding->confidence, 4, '.', ''); $record->safeSummary = $finding->safeSummary; $record->evidencePages = $finding->evidencePages; $record->createdAt = $analysis->createdAt; $this->entityManager->persist($record);
    }

    private function persistAnchor(QuestionImportAnalysis $analysis, QuestionImportImageAnchor $anchor): void
    {
        $record = new QuestionImportImageAnchorRecord(); $record->id = $anchor->id; $record->analysisId = $analysis->id; $record->targetKind = $anchor->target->value; $record->optionPosition = $anchor->optionPosition; $record->sourcePage = $anchor->sourcePage; $record->sourceAssetIndex = $anchor->sourceAssetIndex; $record->status = $anchor->status->value; $record->createdAt = $analysis->createdAt; $this->entityManager->persist($record);
    }

    private function map(QuestionImportAnalysisRecord $record): QuestionImportAnalysis
    {
        $findings = $this->entityManager->createQueryBuilder()->select('finding')->from(QuestionImportAnalysisFindingRecord::class, 'finding')->where('finding.analysisId = :analysis')->setParameter('analysis', $record->id)->orderBy('finding.createdAt', 'ASC')->getQuery()->getResult();
        $anchors = $this->entityManager->createQueryBuilder()->select('anchor')->from(QuestionImportImageAnchorRecord::class, 'anchor')->where('anchor.analysisId = :analysis')->setParameter('analysis', $record->id)->orderBy('anchor.createdAt', 'ASC')->getQuery()->getResult();
        $metadata = is_array($record->metadata) ? $record->metadata : [];
        return new QuestionImportAnalysis($record->id, $record->jobId, $record->questionId, $record->candidateFingerprint, $record->schemaVersion, $record->algorithmVersion, $record->provider, $record->model, array_map('intval', $record->evidencePages), new QuestionImportMetadata($metadata['exam'] ?? null, $metadata['position'] ?? null, $metadata['board'] ?? null, $metadata['year'] ?? null, $metadata['evidence_pages'] ?? []), QuestionImportStructureType::from($record->structureType), QuestionImportAnswerKeyAssessment::from($record->answerKeyAssessment), QuestionImportImageAssessment::from($record->imageAssessment), new QuestionImportTokenUsage(QuestionImportUsageAvailability::from($record->usageAvailability), $record->inputTokens, $record->outputTokens, $record->totalTokens, $record->costUsd), $record->durationMilliseconds, array_map(static fn (QuestionImportAnalysisFindingRecord $finding): QuestionImportFinding => new QuestionImportFinding($finding->id, QuestionImportFindingCode::from($finding->code), QuestionImportFindingSeverity::from($finding->severity), $finding->confidence === null ? null : (float) $finding->confidence, $finding->safeSummary, array_map('intval', $finding->evidencePages)), $findings), array_map(static fn (QuestionImportImageAnchorRecord $anchor): QuestionImportImageAnchor => new QuestionImportImageAnchor($anchor->id, QuestionImportImageTarget::from($anchor->targetKind), $anchor->optionPosition, $anchor->sourcePage, $anchor->sourceAssetIndex, QuestionImportImageAnchorStatus::from($anchor->status)), $anchors), $record->createdAt);
    }
}
