<?php
declare(strict_types=1);

namespace App\Interface\Http\Identity;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class UserAdministrationRequestFactory
{
    /** @return array<string,mixed> */
    public function body(ServerRequestInterface $request): array
    {
        try {
            $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new InvalidArgumentException('JSON inválido.');
        }

        if (!is_array($body) || array_is_list($body)) {
            throw new InvalidArgumentException('O corpo deve ser um objeto JSON.');
        }

        return $body;
    }

    /** @return list<string> */
    public function roles(array $body): array
    {
        $roles = $body['roles'] ?? null;

        if (!is_array($roles) || $roles === []) {
            throw new InvalidArgumentException('Papéis inválidos.');
        }

        foreach ($roles as $role) {
            if (!is_string($role) || trim($role) === '') {
                throw new InvalidArgumentException('Papéis inválidos.');
            }
        }

        return array_values($roles);
    }

    public function requiredString(array $body, string $key): string
    {
        $value = $body[$key] ?? null;

        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException("Campo {$key} inválido.");
        }

        return trim($value);
    }

    public function optionalString(array $body, string $key): ?string
    {
        $value = $body[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException("Campo {$key} inválido.");
        }

        return trim($value);
    }
}
