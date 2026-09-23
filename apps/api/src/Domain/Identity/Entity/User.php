<?php
declare(strict_types=1);
namespace App\Domain\Identity\Entity;
final readonly class User { /** @param list<string> $roles */ public function __construct(public string $id,public string $email,public string $name,public string $passwordHash,public string $status,public array $roles) {} public function isActive(): bool { return $this->status === 'ACTIVE'; } }
