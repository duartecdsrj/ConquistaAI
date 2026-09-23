<?php
declare(strict_types=1);
namespace App\Application\Identity\DTO\Request;
final readonly class RefreshTokenRequestDto { public function __construct(public string $refreshToken, public ?string $deviceName = null) {} }
