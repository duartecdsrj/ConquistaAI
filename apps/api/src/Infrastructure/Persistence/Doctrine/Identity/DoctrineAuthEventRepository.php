<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Identity;

use App\Domain\Identity\Entity\AuthEvent;
use App\Domain\Identity\Repository\AuthEventRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Identity\Entity\AuthEventRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineAuthEventRepository implements AuthEventRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function append(AuthEvent $event): void
    {
        $record = new AuthEventRecord();
        $record->userId = $event->userId;
        $record->event = $event->event;
        $record->ipHash = $event->ipHash;
        $record->occurredAt = $event->occurredAt;

        $this->entityManager->persist($record);
    }
}
