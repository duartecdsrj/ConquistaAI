<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance;

use App\Domain\Performance\Repository\PerformanceStatisticsRepositoryInterface;
use App\Domain\Performance\ValueObject\CompletedAnswer;
use App\Infrastructure\Persistence\Doctrine\Performance\Entity\AnswerRecord;
use App\Infrastructure\Persistence\Doctrine\Performance\Entity\AttemptRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionSubjectRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrinePerformanceStatisticsRepository implements PerformanceStatisticsRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function completedAnswersForUser(string $userId): array
    {
        $rows = $this->entityManager->createQueryBuilder()
            ->select(
                'answer.optionId AS optionId',
                'answer.elapsedSeconds AS elapsedSeconds',
                'question.correctOptionId AS correctOptionId',
                'subject.subjectId AS subjectId',
            )
            ->from(AnswerRecord::class, 'answer')
            ->innerJoin(AttemptRecord::class, 'attempt', 'WITH', 'attempt.id = answer.attemptId')
            ->innerJoin(QuestionRecord::class, 'question', 'WITH', 'question.id = attempt.questionId')
            ->leftJoin(QuestionSubjectRecord::class, 'subject', 'WITH', 'subject.questionId = question.id')
            ->where('attempt.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('answer.submittedAt', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_map(
            static fn (array $row): CompletedAnswer => new CompletedAnswer(
                (string) ($row['subjectId'] ?? 'unclassified'),
                isset($row['correctOptionId']) && $row['optionId'] === $row['correctOptionId'],
                (int) $row['elapsedSeconds'],
            ),
            $rows,
        );
    }
}
