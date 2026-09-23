<?php
declare(strict_types=1);
namespace App\Domain\Identity\Repository;
use App\Domain\Identity\Entity\AuthSession;
interface AuthSessionRepositoryInterface { public function findByRefreshTokenHashForUpdate(string $hash): ?AuthSession; public function save(AuthSession $session): void; public function revoke(string $id, \DateTimeImmutable $at): void; public function revokeFamily(string $familyId, \DateTimeImmutable $at): void; }
