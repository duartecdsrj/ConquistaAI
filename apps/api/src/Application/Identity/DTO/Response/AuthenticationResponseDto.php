<?php
declare(strict_types=1);
namespace App\Application\Identity\DTO\Response;
final readonly class AuthenticationResponseDto { public function __construct(public string $accessToken, public string $refreshToken, public CurrentUserResponseDto $user) {} }
