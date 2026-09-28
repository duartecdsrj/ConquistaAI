<?php
declare(strict_types=1);namespace App\Application\Identity\DTO\Request;
final readonly class CreateAdminUserRequestDto {/** @param list<string> $roles */public function __construct(public string $actorUserId,public string $name,public string $email,public ?string $password,public array $roles,public string $status,public ?string $googleEmail) {}}
