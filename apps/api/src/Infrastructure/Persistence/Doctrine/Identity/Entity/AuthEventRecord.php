<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Identity\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'auth_events')]
class AuthEventRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    public ?int $id = null;

    #[ORM\Column(name: 'user_id', type: 'string', length: 36, nullable: true)]
    public ?string $userId = null;

    #[ORM\Column(type: 'string', length: 64)]
    public string $event;

    #[ORM\Column(name: 'ip_hash', type: 'string', length: 64)]
    public string $ipHash;

    #[ORM\Column(name: 'occurred_at', type: 'datetime_immutable')]
    public DateTimeImmutable $occurredAt;
}
