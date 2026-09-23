<?php
declare(strict_types=1);

namespace App\Application\Identity\Port;

interface PasswordHasherInterface
{
    public function hash(string $plainText): string;

    public function verify(string $plainText, string $hash): bool;
}
