<?php
declare(strict_types=1);namespace App\Application\Identity\DTO\Request;
final readonly class SetGoogleIdentityRequestDto {public function __construct(public string $actorUserId,public string $userId,public string $googleEmail) {}}
