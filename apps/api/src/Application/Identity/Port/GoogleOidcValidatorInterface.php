<?php
declare(strict_types=1);
namespace App\Application\Identity\Port;
final readonly class VerifiedGoogleIdentity
{
    public function __construct(public string $email, public string $name, public ?string $pictureUrl) {}
}
interface GoogleOidcValidatorInterface { public function validate(string $credential): VerifiedGoogleIdentity; }
