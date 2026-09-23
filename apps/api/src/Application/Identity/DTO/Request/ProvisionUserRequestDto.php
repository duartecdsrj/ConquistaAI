<?php
declare(strict_types=1);

namespace App\Application\Identity\DTO\Request;

/** @phpstan-type RoleList non-empty-list<string> */
final readonly class ProvisionUserRequestDto
{
    /** @param non-empty-list<string> $roles */
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
        public array $roles,
    ) {
    }
}
