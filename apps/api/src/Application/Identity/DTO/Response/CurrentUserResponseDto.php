<?php
declare(strict_types=1);
namespace App\Application\Identity\DTO\Response;
/** @phpstan-type RoleList list<string> */
final readonly class CurrentUserResponseDto { /** @param list<string> $roles */ public function __construct(public string $id, public string $email, public string $name, public array $roles) {} }
