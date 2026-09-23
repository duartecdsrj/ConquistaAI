<?php
declare(strict_types=1);

namespace App\Interface\Http\Identity;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\DTO\Request\LoginRequestDto;
use App\Application\Identity\DTO\Request\LogoutRequestDto;
use App\Application\Identity\DTO\Request\RefreshTokenRequestDto;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

final class IdentityRequestFactory
{
    public function login(ServerRequestInterface $request): LoginRequestDto
    {
        $payload = $this->json($request);
        $email = $this->requiredString($payload, 'email');
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Informe um e-mail valido.');
        }

        return new LoginRequestDto(
            $email,
            $this->requiredString($payload, 'password'),
            $this->clientIp($request),
            $this->optionalString($payload, 'device_name'),
        );
    }

    public function refresh(ServerRequestInterface $request): RefreshTokenRequestDto
    {
        $payload = $this->json($request);
        return new RefreshTokenRequestDto(
            $this->refreshToken($request, $payload),
            $this->optionalString($payload, 'device_name'),
        );
    }

    public function logout(ServerRequestInterface $request): LogoutRequestDto
    {
        return new LogoutRequestDto($this->refreshToken($request, $this->json($request)));
    }

    public function accessToken(ServerRequestInterface $request): AccessTokenRequestDto
    {
        $header = $request->getHeaderLine('Authorization');
        if (preg_match('/^Bearer\\s+(.+)$/i', $header, $matches) !== 1) {
            throw new InvalidArgumentException('Token de acesso ausente.');
        }

        return new AccessTokenRequestDto($matches[1]);
    }

    /** @return array<string, mixed> */
    private function json(ServerRequestInterface $request): array
    {
        $body = trim((string) $request->getBody());
        if ($body === '') {
            return [];
        }

        try {
            $payload = json_decode($body, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new InvalidArgumentException('JSON invalido.');
        }

        if (!is_object($payload)) {
            throw new InvalidArgumentException('O corpo deve ser um objeto JSON.');
        }

        return get_object_vars($payload);
    }

    /** @param array<string, mixed> $payload */
    private function requiredString(array $payload, string $field): string
    {
        $value = $payload[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException(sprintf('O campo %s e obrigatorio.', $field));
        }

        return trim($value);
    }

    /** @param array<string, mixed> $payload */
    private function optionalString(array $payload, string $field): ?string
    {
        $value = $payload[$field] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException(sprintf('O campo %s e invalido.', $field));
        }

        return trim($value);
    }

    /** @param array<string, mixed> $payload */
    private function refreshToken(ServerRequestInterface $request, array $payload): string
    {
        $cookieToken = $request->getCookieParams()['refresh_token'] ?? null;
        if (is_string($cookieToken) && $cookieToken !== '') {
            return $cookieToken;
        }

        return $this->requiredString($payload, 'refresh_token');
    }

    private function clientIp(ServerRequestInterface $request): string
    {
        $forwarded = $request->getHeaderLine('X-Forwarded-For');
        if ($forwarded !== '') {
            return trim(explode(',', $forwarded)[0]);
        }

        return $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
