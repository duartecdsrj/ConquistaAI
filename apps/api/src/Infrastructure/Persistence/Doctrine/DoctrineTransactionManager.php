<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Application\Catalog\Port\TransactionManagerInterface as CatalogTransactionManagerInterface;
use App\Application\Identity\Port\TransactionManagerInterface as IdentityTransactionManagerInterface;
use App\Application\Performance\Port\TransactionManagerInterface as PerformanceTransactionManagerInterface;
use App\Application\Study\Port\TransactionManagerInterface as StudyTransactionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTransactionManager implements
    IdentityTransactionManagerInterface,
    CatalogTransactionManagerInterface,
    StudyTransactionManagerInterface,
    PerformanceTransactionManagerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function transactional(callable $callback): mixed
    {
        return $this->entityManager->wrapInTransaction(
            static fn (): mixed => $callback(),
        );
    }
}
