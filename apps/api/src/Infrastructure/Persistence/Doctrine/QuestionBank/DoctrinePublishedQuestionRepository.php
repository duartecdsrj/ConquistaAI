<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Domain\QuestionBank\ReadModel\PublishedQuestion;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionFilter;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionOption;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionPage;
use App\Domain\QuestionBank\Repository\FrozenQuestionReaderInterface;
use App\Domain\QuestionBank\Repository\PublishedQuestionRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrinePublishedQuestionRepository implements PublishedQuestionRepositoryInterface, FrozenQuestionReaderInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function findPublished(PublishedQuestionFilter $filter): PublishedQuestionPage
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('question')
            ->from(QuestionRecord::class, 'question')
            ->where('question.status = :status')
            ->setParameter('status', 'PUBLISHED');

        if ($filter->board !== null) {
            $query->andWhere('question.board = :board')->setParameter('board', $filter->board);
        }
        if ($filter->year !== null) {
            $query->andWhere('question.examYear = :year')->setParameter('year', $filter->year);
        }
        if ($filter->difficulty !== null) {
            $query->andWhere('question.difficulty = :difficulty')->setParameter('difficulty', $filter->difficulty);
        }
        if ($filter->subjectId !== null) {
            $query->innerJoin('App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionSubjectRecord', 'subject', 'WITH', 'subject.questionId = question.id')
                ->andWhere('subject.subjectId = :subjectId')
                ->setParameter('subjectId', $filter->subjectId);
        }

        $countQuery = clone $query;
        $total = (int) $countQuery->select('COUNT(DISTINCT question.id)')->getQuery()->getSingleScalarResult();
        $records = $query->orderBy('question.id', 'ASC')->setFirstResult($filter->offset)->setMaxResults($filter->limit)->getQuery()->getResult();

        return new PublishedQuestionPage(array_map($this->map(...), $records), $total);
    }

    /** @param list<string> $ids @return list<PublishedQuestion> */
    public function findByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids, static fn (mixed $id): bool => is_string($id) && $id !== '')));
        if ($ids === []) {
            return [];
        }

        $records = $this->entityManager->createQueryBuilder()
            ->select('question')
            ->from(QuestionRecord::class, 'question')
            ->where('question.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        return array_map($this->map(...), $records);
    }

    private function map(QuestionRecord $question): PublishedQuestion
    {
        $options = array_map(
            static fn (QuestionOptionRecord $option): PublishedQuestionOption => new PublishedQuestionOption(
                $option->id,
                $option->label,
                $option->content,
                $option->sortOrder,
            ),
            $this->entityManager->createQueryBuilder()
                ->select('option')
                ->from(QuestionOptionRecord::class, 'option')
                ->where('option.questionId = :questionId')
                ->setParameter('questionId', $question->id)
                ->orderBy('option.sortOrder', 'ASC')
                ->getQuery()
                ->getResult(),
        );

        return new PublishedQuestion(
            $question->id,
            $question->statement,
            $question->difficulty,
            $question->board,
            $question->examYear,
            $options,
        );
    }
}
