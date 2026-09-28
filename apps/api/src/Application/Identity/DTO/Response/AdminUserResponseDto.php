<?php
declare(strict_types=1);namespace App\Application\Identity\DTO\Response;
final readonly class AdminUserResponseDto {/** @param list<string> $roles */public function __construct(public string $id,public string $name,public string $email,public ?string $googleEmail,public array $roles,public string $status) {}}
