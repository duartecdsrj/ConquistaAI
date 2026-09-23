<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Identity;

use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Identity\Entity\RoleRecord;
use App\Infrastructure\Persistence\Doctrine\Identity\Entity\UserRecord;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findByEmail(string $email): ?User
    {
        return $this->map($this->query()->where('user.email = :email')->setParameter('email', $email)->getQuery()->getOneOrNullResult());
    }

    public function findById(string $id): ?User
    {
        return $this->map($this->query()->where('user.id = :id')->setParameter('id', $id)->getQuery()->getOneOrNullResult());
    }

    public function save(User $user): void
    {
        $record = new UserRecord();
        $record->id = $user->id;
        $record->email = $user->email;
        $record->name = $user->name;
        $record->passwordHash = $user->passwordHash;
        $record->status = $user->status;
        $record->createdAt = new DateTimeImmutable('now');
        $record->updatedAt = $record->createdAt;

        $roles = $this->entityManager->createQueryBuilder()
            ->select('role')
            ->from(RoleRecord::class, 'role')
            ->where('role.code IN (:codes)')
            ->setParameter('codes', $user->roles)
            ->getQuery()
            ->getResult();

        if (count($roles) !== count($user->roles)) {
            throw new \InvalidArgumentException('Papel inexistente.');
        }
        foreach ($roles as $role) {
            $record->roles->add($role);
        }

        $this->entityManager->persist($record);
    }

    private function query(): \Doctrine\ORM\QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select('user', 'role')
            ->from(UserRecord::class, 'user')
            ->leftJoin('user.roles', 'role');
    }

    private function map(?UserRecord $record): ?User
    {
        if ($record === null) {
            return null;
        }

        return new User(
            $record->id,
            $record->email,
            $record->name,
            $record->passwordHash,
            $record->status,
            array_values($record->roles->map(static fn (RoleRecord $role): string => $role->code)->toArray()),
        );
    }
}
