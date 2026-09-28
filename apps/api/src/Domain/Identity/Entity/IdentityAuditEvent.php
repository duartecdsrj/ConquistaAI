<?php
declare(strict_types=1);
namespace App\Domain\Identity\Entity;
/** @phpstan-type AuditValues array<string, scalar|null> */
final readonly class IdentityAuditEvent
{
    /** @param array<string, scalar|null>|null $previousValues @param array<string, scalar|null>|null $newValues */
    public function __construct(public ?string $actorUserId, public string $subjectUserId, public string $event, public ?array $previousValues, public ?array $newValues, public \DateTimeImmutable $occurredAt) {}
}
