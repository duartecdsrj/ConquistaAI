<?php
declare(strict_types=1);

namespace App\Application\Identity\Port;

final readonly class AccessTokenClaims
{
    public function __construct(public string $subject) {}
}
