<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Domain\QuestionBank\Entity\QuestionQualitySignal;
use App\Domain\QuestionBank\Enum\QuestionQualityCategory;
use App\Domain\QuestionBank\Repository\QuestionQualitySignalRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionQualitySignalRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineQuestionQualitySignalRepository implements QuestionQualitySignalRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}
    public function replace(QuestionQualitySignal $signal): void
    {
        $record = $this->entityManager->find(QuestionQualitySignalRecord::class, $signal->questionId) ?? new QuestionQualitySignalRecord();
        $record->questionId = $signal->questionId; $record->analysisId = $signal->analysisId; $record->category = $signal->category->value; $record->safeMessage = $signal->safeMessage; $record->detectedAt = $signal->detectedAt; $this->entityManager->persist($record);
    }
    public function findForQuestion(string $questionId): ?QuestionQualitySignal
    {
        $record = $this->entityManager->find(QuestionQualitySignalRecord::class, $questionId);
        return $record instanceof QuestionQualitySignalRecord ? new QuestionQualitySignal($record->questionId, $record->analysisId, QuestionQualityCategory::from($record->category), $record->safeMessage, $record->detectedAt) : null;
    }
}
