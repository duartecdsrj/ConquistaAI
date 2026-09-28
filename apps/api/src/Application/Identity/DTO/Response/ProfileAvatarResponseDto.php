<?php
declare(strict_types=1);namespace App\Application\Identity\DTO\Response;
final readonly class ProfileAvatarResponseDto { public function __construct(public bool $available, public ?string $mimeType, public ?string $updatedAt) {} }
