<?php
declare(strict_types=1);
namespace App\Domain\Identity\Entity;
use App\Domain\Identity\ValueObject\GoogleEmail;
final readonly class GoogleIdentity
{
    public function __construct(public string $userId, public GoogleEmail $email, public \DateTimeImmutable $createdAt, public \DateTimeImmutable $updatedAt) {}
}
