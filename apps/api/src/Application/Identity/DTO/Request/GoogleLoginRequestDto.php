<?php
declare(strict_types=1);
namespace App\Application\Identity\DTO\Request;
final readonly class GoogleLoginRequestDto { public function __construct(public string $credential, public ?string $deviceName) {} }
