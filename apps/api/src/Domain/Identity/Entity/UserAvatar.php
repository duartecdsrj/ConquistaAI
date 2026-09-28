<?php
declare(strict_types=1);
namespace App\Domain\Identity\Entity;
use App\Domain\Identity\Enum\AvatarSource;
final readonly class UserAvatar
{
    public function __construct(public string $storageKey, public string $mimeType, public AvatarSource $source, public \DateTimeImmutable $updatedAt) {}
}
