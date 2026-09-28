<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Identity;
use App\Domain\Identity\Entity\IdentityAuditEvent;
use App\Domain\Identity\Repository\IdentityAuditEventRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Identity\Entity\IdentityAuditEventRecord;
use Doctrine\ORM\EntityManagerInterface;
final class DoctrineIdentityAuditEventRepository implements IdentityAuditEventRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}
    public function append(IdentityAuditEvent $event): void { $record=new IdentityAuditEventRecord();$record->actorUserId=$event->actorUserId;$record->subjectUserId=$event->subjectUserId;$record->event=$event->event;$record->previousValues=$event->previousValues;$record->newValues=$event->newValues;$record->occurredAt=$event->occurredAt;$this->entityManager->persist($record); }
}
