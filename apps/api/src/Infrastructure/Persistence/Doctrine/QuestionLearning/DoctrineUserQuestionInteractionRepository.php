<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\QuestionLearning;
use App\Domain\QuestionLearning\Entity\UserQuestionInteraction;
use App\Domain\QuestionLearning\Repository\UserQuestionInteractionRepositoryInterface;
use App\Domain\QuestionLearning\ValueObject\QuestionInteractionState;
use App\Infrastructure\Persistence\Doctrine\QuestionLearning\Entity\UserQuestionInteractionRecord;
use Doctrine\ORM\EntityManagerInterface;
final class DoctrineUserQuestionInteractionRepository implements UserQuestionInteractionRepositoryInterface {
    public function __construct(private readonly EntityManagerInterface $entityManager) {}
    public function find(string $userId, string $questionId): ?UserQuestionInteraction { $record = $this->entityManager->find(UserQuestionInteractionRecord::class, ['userId' => $userId, 'questionId' => $questionId]); return $record instanceof UserQuestionInteractionRecord ? $this->map($record) : null; }
    public function save(UserQuestionInteraction $interaction): void { $record = $this->entityManager->find(UserQuestionInteractionRecord::class, ['userId' => $interaction->userId, 'questionId' => $interaction->questionId]) ?? new UserQuestionInteractionRecord(); $state = $interaction->state(); $record->userId = $interaction->userId; $record->questionId = $interaction->questionId; $record->favorite = $state->favorite; $record->reviewLater = $state->reviewLater; $record->notMastered = $state->notMastered; $record->createdAt = $interaction->createdAt; $record->updatedAt = $interaction->updatedAt(); $this->entityManager->persist($record); $this->entityManager->flush(); }
    private function map(UserQuestionInteractionRecord $record): UserQuestionInteraction { return new UserQuestionInteraction($record->userId, $record->questionId, new QuestionInteractionState($record->favorite, $record->reviewLater, $record->notMastered), $record->createdAt, $record->updatedAt); }
}
