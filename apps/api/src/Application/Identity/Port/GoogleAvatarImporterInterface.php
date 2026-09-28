<?php
declare(strict_types=1);
namespace App\Application\Identity\Port;
use App\Domain\Identity\Entity\UserAvatar;
interface GoogleAvatarImporterInterface { public function import(string $userId, ?string $pictureUrl): ?UserAvatar; }
