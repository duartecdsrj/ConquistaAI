<?php
declare(strict_types=1);
namespace App\Application\Identity\DTO\Request;
final readonly class LoginRequestDto { public function __construct(public string $email, public string $password, public string $ipAddress, public ?string $deviceName = null) {} }
