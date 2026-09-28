<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Identity\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: 'identity_audit_events')]
class IdentityAuditEventRecord
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint')] public ?int $id = null;
    #[ORM\Column(name: 'actor_user_id', type: 'string', length: 36, nullable: true)] public ?string $actorUserId;
    #[ORM\Column(name: 'subject_user_id', type: 'string', length: 36)] public string $subjectUserId;
    #[ORM\Column(type: 'string', length: 64)] public string $event;
    #[ORM\Column(name: 'previous_values', type: 'json', nullable: true)] public ?array $previousValues;
    #[ORM\Column(name: 'new_values', type: 'json', nullable: true)] public ?array $newValues;
    #[ORM\Column(name: 'occurred_at', type: 'datetime_immutable')] public \DateTimeImmutable $occurredAt;
}
