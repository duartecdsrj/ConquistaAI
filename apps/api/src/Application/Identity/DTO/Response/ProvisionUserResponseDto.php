<?php
declare(strict_types=1);

namespace App\Application\Identity\DTO\Response;

final readonly class ProvisionUserResponseDto
{
    /** @param list<string> $roles */
    public function __construct(
        public string $id,
        public string $email,
        public string $name,
        public array $roles,
    ) {
    }
}
