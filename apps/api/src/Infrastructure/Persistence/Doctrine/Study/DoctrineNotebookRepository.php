<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Study;

use App\Domain\Study\Entity\Notebook;
use App\Domain\Study\Enum\NotebookMode;
use App\Domain\Study\Enum\NotebookStatus;
use App\Domain\Study\Repository\NotebookRepositoryInterface;
use App\Domain\Study\ValueObject\FrozenQuestionSelection;
use App\Infrastructure\Persistence\Doctrine\Study\Entity\NotebookQuestionRecord;
use App\Infrastructure\Persistence\Doctrine\Study\Entity\NotebookRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineNotebookRepository implements NotebookRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(Notebook $notebook): void
    {
        $record = $this->entityManager->find(NotebookRecord::class, $notebook->id);
        if ($record instanceof NotebookRecord) {
            $this->synchronize($record, $notebook);

            return;
        }

        $record = new NotebookRecord();
        $record->id = $notebook->id;
        $record->userId = $notebook->userId;
        $record->name = $notebook->name;
        $record->type = 'PRACTICE';
        $record->mode = $notebook->mode->value;
        $record->filters = [];
        $record->createdAt = $notebook->createdAt;
        $this->synchronize($record, $notebook);
        $this->entityManager->persist($record);

        foreach ($notebook->selection->questionIds as $position => $questionId) {
            $selection = new NotebookQuestionRecord();
            $selection->notebookId = $notebook->id;
            $selection->questionId = $questionId;
            $selection->position = $position + 1;
            $this->entityManager->persist($selection);
        }
    }

    public function findByIdForUser(string $id, string $userId): ?Notebook
    {
        $record = $this->entityManager->createQueryBuilder()
            ->select('notebook')
            ->from(NotebookRecord::class, 'notebook')
            ->where('notebook.id = :id')
            ->andWhere('notebook.userId = :userId')
            ->setParameter('id', $id)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        return $record instanceof NotebookRecord ? $this->map($record) : null;
    }

    /** @return list<Notebook> */
    public function listForUser(string $userId, int $offset, int $limit): array
    {
        return array_map(
            fn (NotebookRecord $record): Notebook => $this->map($record),
            $this->entityManager->createQueryBuilder()
                ->select('notebook')
                ->from(NotebookRecord::class, 'notebook')
                ->where('notebook.userId = :userId')
                ->setParameter('userId', $userId)
                ->orderBy('notebook.createdAt', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult(),
        );
    }

    public function countForUser(string $userId): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(notebook.id)')
            ->from(NotebookRecord::class, 'notebook')
            ->where('notebook.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function synchronize(NotebookRecord $record, Notebook $notebook): void
    {
        $record->status = $notebook->status->value;
        $record->startedAt = $notebook->startedAt;
        $record->finishedAt = $notebook->finishedAt;
        $record->durationSeconds = $notebook->durationSeconds;
        $record->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    private function map(NotebookRecord $record): Notebook
    {
        $questionIds = array_map(
            static fn (NotebookQuestionRecord $selection): string => $selection->questionId,
            $this->entityManager->createQueryBuilder()
                ->select('selection')
                ->from(NotebookQuestionRecord::class, 'selection')
                ->where('selection.notebookId = :notebookId')
                ->setParameter('notebookId', $record->id)
                ->orderBy('selection.position', 'ASC')
                ->getQuery()
                ->getResult(),
        );

        return new Notebook(
            $record->id,
            $record->userId,
            $record->name,
            NotebookMode::from($record->mode),
            FrozenQuestionSelection::fromQuestionIds($questionIds, count($questionIds)),
            $record->createdAt,
            NotebookStatus::from($record->status),
            $record->startedAt,
            $record->finishedAt,
            $record->durationSeconds,
        );
    }
}
