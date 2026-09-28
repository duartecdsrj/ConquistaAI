<?php
declare(strict_types=1);

namespace App\Domain\Identity\Repository;

use App\Domain\Identity\Entity\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function findById(string $id): ?User;

    public function save(User $user): void;
    /** @return list<User> */ public function list(int $offset, int $limit, ?string $query, ?string $status): array;
    public function count(?string $query, ?string $status): int;
    public function countActiveAdmins(): int;
}
