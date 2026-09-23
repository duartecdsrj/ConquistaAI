<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Identity;

use App\Domain\Identity\Entity\AuthSession;
use App\Domain\Identity\Repository\AuthSessionRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Identity\Entity\AuthSessionRecord;
use DateTimeImmutable;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineAuthSessionRepository implements AuthSessionRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findByRefreshTokenHashForUpdate(string $hash): ?AuthSession
    {
        $record = $this->entityManager->createQueryBuilder()
            ->select('session')
            ->from(AuthSessionRecord::class, 'session')
            ->where('session.refreshTokenHash = :hash')
            ->setParameter('hash', $hash)
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getOneOrNullResult();

        return $record instanceof AuthSessionRecord ? $this->map($record) : null;
    }

    public function save(AuthSession $session): void
    {
        $record = new AuthSessionRecord();
        $record->id = $session->id;
        $record->userId = $session->userId;
        $record->familyId = $session->familyId;
        $record->refreshTokenHash = $session->refreshTokenHash;
        $record->expiresAt = $session->expiresAt;
        $record->revokedAt = $session->revokedAt;
        $record->deviceName = $session->deviceName;
        $record->createdAt = new DateTimeImmutable('now');

        $this->entityManager->persist($record);
    }

    public function revoke(string $id, DateTimeImmutable $at): void
    {
        $this->entityManager->createQueryBuilder()
            ->update(AuthSessionRecord::class, 'session')
            ->set('session.revokedAt', ':revokedAt')
            ->where('session.id = :id')
            ->andWhere('session.revokedAt IS NULL')
            ->setParameter('id', $id)
            ->setParameter('revokedAt', $at)
            ->getQuery()
            ->execute();
    }

    public function revokeFamily(string $familyId, DateTimeImmutable $at): void
    {
        $this->entityManager->createQueryBuilder()
            ->update(AuthSessionRecord::class, 'session')
            ->set('session.revokedAt', ':revokedAt')
            ->where('session.familyId = :familyId')
            ->andWhere('session.revokedAt IS NULL')
            ->setParameter('familyId', $familyId)
            ->setParameter('revokedAt', $at)
            ->getQuery()
            ->execute();
    }

    private function map(AuthSessionRecord $record): AuthSession
    {
        return new AuthSession(
            $record->id,
            $record->userId,
            $record->familyId,
            $record->refreshTokenHash,
            $record->expiresAt,
            $record->revokedAt,
            $record->deviceName,
        );
    }
}
