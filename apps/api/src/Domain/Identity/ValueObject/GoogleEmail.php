<?php
declare(strict_types=1);
namespace App\Domain\Identity\ValueObject;
final readonly class GoogleEmail
{
    private function __construct(public string $value) {}
    public static function from(string $email): self
    {
        $normalized = mb_strtolower(trim($email));
        if ($normalized === '' || filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Informe um e-mail Google válido.');
        }
        return new self($normalized);
    }
}
