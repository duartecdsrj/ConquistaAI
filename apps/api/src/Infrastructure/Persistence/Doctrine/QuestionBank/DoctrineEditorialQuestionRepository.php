<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Domain\QuestionBank\ReadModel\PublishedQuestion;
use App\Domain\QuestionBank\Repository\EditorialQuestionRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineEditorialQuestionRepository implements EditorialQuestionRepositoryInterface
{
    private const EDITORIAL_STATUSES = ['REVIEW', 'DRAFT', 'VOID'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DoctrinePublishedQuestionRepository $reader,
    ) {}

    public function listDrafts(int $offset, int $limit): array
    {
        $records = $this->em->createQueryBuilder()
            ->select('question')
            ->addSelect("CASE question.status WHEN 'REVIEW' THEN 1 WHEN 'DRAFT' THEN 2 WHEN 'VOID' THEN 3 ELSE 4 END AS HIDDEN statusOrder")
            ->from(QuestionRecord::class, 'question')
            ->where('question.status IN (:statuses)')
            ->setParameter('statuses', self::EDITORIAL_STATUSES)
            ->orderBy('statusOrder', 'ASC')
            ->addOrderBy('question.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        $ids = array_map(static fn (QuestionRecord $question): string => $question->id, $records);

        return [
            'items' => $this->reader->findByIds($ids),
            'total' => (int) $this->em->createQueryBuilder()
                ->select('COUNT(question.id)')
                ->from(QuestionRecord::class, 'question')
                ->where('question.status IN (:statuses)')
                ->setParameter('statuses', self::EDITORIAL_STATUSES)
                ->getQuery()
                ->getSingleScalarResult(),
        ];
    }

    public function exists(string $id): bool
    {
        return (int) $this->em->createQueryBuilder()->select('COUNT(question.id)')->from(QuestionRecord::class, 'question')->where('question.id = :id')->setParameter('id', $id)->getQuery()->getSingleScalarResult() > 0;
    }

    public function markForApproval(array $ids): int
    {
        $ids = array_values(array_unique(array_filter($ids, static fn (mixed $id): bool => is_string($id) && $id !== '')));
        if ($ids === []) return 0;
        return $this->em->createQueryBuilder()->update(QuestionRecord::class, 'question')->set('question.status', ':draft')->set('question.updatedAt', ':now')->where('question.id IN (:ids)')->andWhere('question.status = :review')->setParameter('draft', 'DRAFT')->setParameter('review', 'REVIEW')->setParameter('now', new \DateTimeImmutable('now'))->setParameter('ids', $ids)->getQuery()->execute();
    }

    public function publishMany(array $ids): int
    {
        $ids = array_values(array_unique(array_filter($ids, static fn (mixed $id): bool => is_string($id) && $id !== '')));
        if ($ids === []) return 0;
        return $this->em->createQueryBuilder()->update(QuestionRecord::class, 'question')->set('question.status', ':published')->set('question.updatedAt', ':now')->where('question.id IN (:ids)')->andWhere('question.status = :draft')->andWhere('question.correctOptionId IS NOT NULL')->setParameter('published', 'PUBLISHED')->setParameter('draft', 'DRAFT')->setParameter('now', new \DateTimeImmutable('now'))->setParameter('ids', $ids)->getQuery()->execute();
    }

    public function publish(string $id): bool
    {
        return $this->em->createQueryBuilder()->update(QuestionRecord::class, 'question')->set('question.status', ':published')->set('question.updatedAt', ':now')->where('question.id = :id')->andWhere('question.status = :draft')->andWhere('question.correctOptionId IS NOT NULL')->setParameter('published', 'PUBLISHED')->setParameter('draft', 'DRAFT')->setParameter('now', new \DateTimeImmutable('now'))->setParameter('id', $id)->getQuery()->execute() === 1;
    }
}
