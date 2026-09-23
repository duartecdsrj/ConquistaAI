<?php
declare(strict_types=1);
namespace App\Application\Identity\DTO\Request;
final readonly class LogoutRequestDto { public function __construct(public string $refreshToken) {} }
