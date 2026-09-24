<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance;

use App\Domain\Performance\Repository\PerformanceStatisticsRepositoryInterface;
use App\Domain\Performance\ValueObject\SyllabusCompletedAnswer;
use App\Domain\Performance\ValueObject\SyllabusOption;
use App\Domain\Performance\ValueObject\TaxonomyHierarchyNode;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\ExamRecord;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\PositionRecord;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\SyllabusRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionTaxonomySubjectRecord;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\Entity\TaxonomySubjectRecord;
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

    public function syllabiWithCompletedAnswersForUser(string $userId): array
    {
        $rows = $this->entityManager->createQueryBuilder()->select('DISTINCT syllabus.id AS id, syllabus.name AS name, position.name AS positionName, exam.name AS examName')->from(AttemptRecord::class, 'attempt')->innerJoin(QuestionRecord::class, 'question', 'WITH', 'question.id = attempt.questionId')->innerJoin(SyllabusRecord::class, 'syllabus', 'WITH', 'syllabus.id = question.syllabusId')->innerJoin(PositionRecord::class, 'position', 'WITH', 'position.id = syllabus.positionId')->innerJoin(ExamRecord::class, 'exam', 'WITH', 'exam.id = position.examId')->where('attempt.userId = :userId')->andWhere('attempt.completedAt IS NOT NULL')->setParameter('userId', $userId)->orderBy('exam.name')->addOrderBy('position.name')->addOrderBy('syllabus.name')->getQuery()->getArrayResult();
        return array_map(static fn (array $row): SyllabusOption => new SyllabusOption((string) $row['id'], (string) $row['name'], (string) $row['positionName'], (string) $row['examName']), $rows);
    }

    public function completedAnswersForUserAndSyllabus(string $userId, string $syllabusId): array
    {
        $rows = $this->entityManager->createQueryBuilder()->select('answer.id AS answerId, answer.optionId AS optionId, answer.elapsedSeconds AS elapsedSeconds, attempt.completedAt AS completedAt, question.correctOptionId AS correctOptionId, assignment.taxonomySubjectId AS taxonomySubjectId')->from(AnswerRecord::class, 'answer')->innerJoin(AttemptRecord::class, 'attempt', 'WITH', 'attempt.id = answer.attemptId')->innerJoin(QuestionRecord::class, 'question', 'WITH', 'question.id = attempt.questionId')->leftJoin(QuestionTaxonomySubjectRecord::class, 'assignment', 'WITH', 'assignment.questionId = question.id')->where('attempt.userId = :userId')->andWhere('attempt.completedAt IS NOT NULL')->andWhere('attempt.finalAnswerId = answer.id')->andWhere('question.syllabusId = :syllabusId')->setParameter('userId', $userId)->setParameter('syllabusId', $syllabusId)->orderBy('answer.id')->getQuery()->getArrayResult();
        $answers = []; foreach ($rows as $row) { $id = (string) $row['answerId']; if (!isset($answers[$id])) $answers[$id] = ['correct' => isset($row['correctOptionId']) && $row['optionId'] === $row['correctOptionId'], 'elapsed' => (int) $row['elapsedSeconds'], 'completedAt' => $row['completedAt'], 'subjects' => []]; if ($row['taxonomySubjectId'] !== null) $answers[$id]['subjects'][] = (string) $row['taxonomySubjectId']; }
        return array_map(static fn (array $answer): SyllabusCompletedAnswer => new SyllabusCompletedAnswer($answer['correct'], $answer['elapsed'], $answer['completedAt'], $answer['subjects']), array_values($answers));
    }

    public function taxonomyHierarchyForSyllabus(string $syllabusId): array
    {
        $rows = $this->entityManager->createQueryBuilder()->select('taxonomy.id AS id, taxonomy.parentId AS parentId, taxonomy.name AS name')->from(TaxonomySubjectRecord::class, 'taxonomy')->where('taxonomy.active = true')->getQuery()->getArrayResult();
        return array_map(static fn (array $row): TaxonomyHierarchyNode => new TaxonomyHierarchyNode((string) $row['id'], $row['parentId'] === null ? null : (string) $row['parentId'], (string) $row['name']), $rows);
    }
}
