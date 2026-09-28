<?php
declare(strict_types=1);
namespace App\Application\Identity\DTO\Response;
final readonly class GoogleAuthenticationResponseDto
{
    private function __construct(public ?AuthenticationResponseDto $authentication, public ?string $status, public ?string $message) {}
    public static function authenticated(AuthenticationResponseDto $authentication): self { return new self($authentication,null,null); }
    public static function pending(): self { return new self(null,'PENDING_APPROVAL','Seu acesso aguarda liberação administrativa.'); }
}
