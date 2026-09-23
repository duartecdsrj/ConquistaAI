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
        $firstSubject = $this->entityManager->createQueryBuilder()
            ->select('MIN(subjectSelection.subjectId)')
            ->from(QuestionSubjectRecord::class, 'subjectSelection')
            ->where('subjectSelection.questionId = question.id');

        $rows = $this->entityManager->createQueryBuilder()
            ->select(
                'answer.optionId AS optionId',
                'answer.elapsedSeconds AS elapsedSeconds',
                'question.correctOptionId AS correctOptionId',
                sprintf('(%s) AS subjectId', $firstSubject->getDQL()),
            )
            ->from(AnswerRecord::class, 'answer')
            ->innerJoin(AttemptRecord::class, 'attempt', 'WITH', 'attempt.id = answer.attemptId')
            ->innerJoin(QuestionRecord::class, 'question', 'WITH', 'question.id = attempt.questionId')
            ->where('attempt.userId = :userId')
            ->andWhere('attempt.completedAt IS NOT NULL')
            ->andWhere('attempt.finalAnswerId = answer.id')
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
