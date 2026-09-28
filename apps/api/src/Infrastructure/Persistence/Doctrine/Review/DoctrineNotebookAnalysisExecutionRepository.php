<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Review;

use App\Domain\Review\Entity\NotebookAnalysisExecution;
use App\Domain\Review\Enum\NotebookAnalysisStatus;
use App\Domain\Review\Repository\NotebookAnalysisExecutionRepositoryInterface;
use App\Domain\Review\ValueObject\NotebookAnalysisJob;
use App\Infrastructure\Persistence\Doctrine\Review\Entity\NotebookAnalysisExecutionRecord;
use App\Infrastructure\Persistence\Doctrine\Study\Entity\NotebookRecord;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineNotebookAnalysisExecutionRepository implements NotebookAnalysisExecutionRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function findByNotebookAndVersion(string $notebookId, string $version): ?NotebookAnalysisExecution
    {
        $record = $this->em->getRepository(NotebookAnalysisExecutionRecord::class)->findOneBy(['notebookId' => $notebookId, 'algorithmVersion' => $version]);
        return $record instanceof NotebookAnalysisExecutionRecord ? $this->map($record) : null;
    }

    public function findForNotebookUser(string $notebookId, string $userId): ?NotebookAnalysisExecution
    {
        $record = $this->em->createQueryBuilder()->select('e')->from(NotebookAnalysisExecutionRecord::class, 'e')->innerJoin(NotebookRecord::class, 'n', 'WITH', 'n.id=e.notebookId')->where('e.notebookId=:notebookId')->andWhere('n.userId=:userId')->setParameter('notebookId', $notebookId)->setParameter('userId', $userId)->orderBy('e.requestedAt', 'DESC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
        return $record instanceof NotebookAnalysisExecutionRecord ? $this->map($record) : null;
    }

    public function claimNextPending(\DateTimeImmutable $now): ?NotebookAnalysisJob
    {
        return $this->em->wrapInTransaction(function () use ($now): ?NotebookAnalysisJob {
            $row = $this->em->createQueryBuilder()->select('e', 'n.userId AS ownerId')->from(NotebookAnalysisExecutionRecord::class, 'e')->innerJoin(NotebookRecord::class, 'n', 'WITH', 'n.id=e.notebookId')->where('e.status IN (:statuses)')->andWhere('e.retryCount < :maxRetries')->setParameter('statuses', [NotebookAnalysisStatus::PENDING->value, NotebookAnalysisStatus::FAILED->value])->setParameter('maxRetries', 3)->orderBy('e.requestedAt', 'ASC')->setMaxResults(1)->getQuery()->setLockMode(LockMode::PESSIMISTIC_WRITE)->getOneOrNullResult();
            if (!is_array($row) || !($row[0] ?? null) instanceof NotebookAnalysisExecutionRecord || !is_string($row['ownerId'] ?? null)) return null;
            $record = $row[0];
            $record->status = NotebookAnalysisStatus::PROCESSING->value;
            $record->startedAt = $now;
            $record->completedAt = null;
            $this->em->persist($record);
            return new NotebookAnalysisJob($this->map($record), $row['ownerId']);
        });
    }

    public function save(NotebookAnalysisExecution $execution): void
    {
        $record = $this->em->find(NotebookAnalysisExecutionRecord::class, $execution->id) ?? new NotebookAnalysisExecutionRecord();
        foreach (['id', 'notebookId', 'algorithmVersion', 'retryCount', 'provider', 'model', 'tokenCount', 'durationMilliseconds', 'summary', 'errorCode', 'errorMessage', 'requestedAt', 'startedAt', 'completedAt'] as $property) $record->$property = $execution->$property;
        $record->status = $execution->status->value;
        $this->em->persist($record);
    }

    private function map(NotebookAnalysisExecutionRecord $record): NotebookAnalysisExecution
    {
        return new NotebookAnalysisExecution($record->id, $record->notebookId, $record->algorithmVersion, NotebookAnalysisStatus::from($record->status), $record->retryCount, $record->provider, $record->model, $record->tokenCount, $record->durationMilliseconds, $record->summary, $record->errorCode, $record->errorMessage, $record->requestedAt, $record->startedAt, $record->completedAt);
    }
}
