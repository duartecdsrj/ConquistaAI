<?php
declare(strict_types=1);
namespace App\Domain\Identity\Enum;
enum UserStatus: string
{
    case ACTIVE = 'ACTIVE';
    case PENDING_APPROVAL = 'PENDING_APPROVAL';
    case BLOCKED = 'BLOCKED';
    public function mayAuthenticate(): bool { return $this === self::ACTIVE; }
}
