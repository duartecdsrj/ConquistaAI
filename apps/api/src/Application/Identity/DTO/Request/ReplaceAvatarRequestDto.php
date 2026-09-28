<?php
declare(strict_types=1);namespace App\Application\Identity\DTO\Request;
final readonly class ReplaceAvatarRequestDto { public function __construct(public string $userId, public string $contents) {} }
