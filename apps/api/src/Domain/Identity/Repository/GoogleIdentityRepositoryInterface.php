<?php
declare(strict_types=1);
namespace App\Domain\Identity\Repository;
use App\Domain\Identity\Entity\GoogleIdentity;
use App\Domain\Identity\ValueObject\GoogleEmail;
interface GoogleIdentityRepositoryInterface
{
    public function findByEmail(GoogleEmail $email): ?GoogleIdentity;
    public function findByUserId(string $userId): ?GoogleIdentity;
    public function save(GoogleIdentity $identity): void;
    public function removeForUserId(string $userId): void;
}
