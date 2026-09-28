<?php
declare(strict_types=1);
namespace App\Domain\Identity\Enum;
enum AvatarSource: string { case GOOGLE = 'GOOGLE'; case MANUAL = 'MANUAL'; }
