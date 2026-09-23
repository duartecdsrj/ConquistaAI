<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance;

use App\Domain\Performance\Entity\Answer;
use App\Domain\Performance\Entity\Attempt;
use App\Domain\Performance\Repository\AttemptRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Performance\Entity\AnswerRecord;
use App\Infrastructure\Persistence\Doctrine\Performance\Entity\AttemptRecord;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineAttemptRepository implements AttemptRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function saveAttempt(Attempt $attempt): void
    {
        $record = new AttemptRecord();
        $record->id = $attempt->id;
        $record->userId = $attempt->userId;
        $record->notebookId = $attempt->notebookId;
        $record->questionId = $attempt->questionId;
        $record->number = $attempt->number;
        $record->context = $attempt->context;
        $record->startedAt = $attempt->startedAt;
        $record->completedAt = $attempt->completedAt;
        $record->createdAt = new DateTimeImmutable('now');
        $this->entityManager->persist($record);
    }

    public function findByIdForUser(string $attemptId, string $userId): ?Attempt
    {
        $record = $this->entityManager->createQueryBuilder()
            ->select('attempt')
            ->from(AttemptRecord::class, 'attempt')
            ->where('attempt.id = :attemptId')
            ->andWhere('attempt.userId = :userId')
            ->setParameter('attemptId', $attemptId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        return $record instanceof AttemptRecord ? new Attempt(
            $record->id,
            $record->userId,
            $record->notebookId,
            $record->questionId,
            $record->number,
            $record->context,
            $record->startedAt,
            $record->completedAt,
        ) : null;
    }

    public function nextNumber(string $userId, string $notebookId, string $questionId): int
    {
        return 1 + (int) $this->entityManager->createQueryBuilder()
            ->select('COALESCE(MAX(attempt.number), 0)')
            ->from(AttemptRecord::class, 'attempt')
            ->where('attempt.userId = :userId')
            ->andWhere('attempt.notebookId = :notebookId')
            ->andWhere('attempt.questionId = :questionId')
            ->setParameter('userId', $userId)
            ->setParameter('notebookId', $notebookId)
            ->setParameter('questionId', $questionId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function appendAnswer(Answer $answer): void
    {
        $record = new AnswerRecord();
        $record->id = $answer->id;
        $record->attemptId = $answer->attemptId;
        $record->optionId = $answer->optionId;
        $record->sequence = $answer->sequence;
        $record->elapsedSeconds = $answer->elapsedSeconds;
        $record->submittedAt = $answer->submittedAt;
        $record->createdAt = new DateTimeImmutable('now');
        $this->entityManager->persist($record);
    }

    public function listAnswers(string $attemptId): array
    {
        return array_map(
            static fn (AnswerRecord $record): Answer => new Answer(
                $record->id,
                $record->attemptId,
                $record->optionId,
                $record->sequence,
                $record->elapsedSeconds,
                $record->submittedAt,
            ),
            $this->entityManager->createQueryBuilder()
                ->select('answer')
                ->from(AnswerRecord::class, 'answer')
                ->where('answer.attemptId = :attemptId')
                ->setParameter('attemptId', $attemptId)
                ->orderBy('answer.sequence', 'ASC')
                ->getQuery()
                ->getResult(),
        );
    }
}
