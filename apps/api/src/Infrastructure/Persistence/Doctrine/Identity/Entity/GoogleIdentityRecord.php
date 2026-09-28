<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Identity\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: 'user_google_identities')]
class GoogleIdentityRecord
{
    #[ORM\Id]
    #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId;
    #[ORM\Column(name: 'google_email', type: 'string', length: 190, unique: true)] public string $googleEmail;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public \DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public \DateTimeImmutable $updatedAt;
}
