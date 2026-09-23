<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Identity\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'auth_sessions')]
class AuthSessionRecord
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id;

    #[ORM\Column(name: 'user_id', type: 'string', length: 36)]
    public string $userId;

    #[ORM\Column(name: 'family_id', type: 'string', length: 36)]
    public string $familyId;

    #[ORM\Column(name: 'refresh_token_hash', type: 'string', length: 64, unique: true)]
    public string $refreshTokenHash;

    #[ORM\Column(name: 'expires_at', type: 'datetime_immutable')]
    public DateTimeImmutable $expiresAt;

    #[ORM\Column(name: 'revoked_at', type: 'datetime_immutable', nullable: true)]
    public ?DateTimeImmutable $revokedAt = null;

    #[ORM\Column(name: 'device_name', type: 'string', length: 255, nullable: true)]
    public ?string $deviceName = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;
}
