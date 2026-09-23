<?php
declare(strict_types=1);
namespace App\Application\Identity\Port;
use App\Domain\Identity\Entity\User;
interface AccessTokenIssuerInterface { public function issue(User $user): string; }
