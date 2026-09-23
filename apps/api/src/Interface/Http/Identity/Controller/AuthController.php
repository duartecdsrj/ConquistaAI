<?php
declare(strict_types=1);

namespace App\Interface\Http\Identity\Controller;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\DTO\Request\LoginRequestDto;
use App\Application\Identity\DTO\Request\LogoutRequestDto;
use App\Application\Identity\DTO\Request\RefreshTokenRequestDto;
use App\Application\Identity\Mapper\IdentityResponseMapper;
use App\Application\Identity\Service\AuthService;
use App\Domain\Identity\Exception\InvalidCredentialsException;
use App\Domain\Identity\Exception\InvalidSessionException;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Infrastructure\Database;
use App\Infrastructure\Http\ApiResponseFactory;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AuthController
{
    public function __construct(
        private readonly AuthService $service,
        private readonly IdentityResponseMapper $mapper,
        private readonly ApiResponseFactory $responses,
    ) {
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, LoginRequestDto $input): ResponseInterface
    {
        try {
            $authentication = $this->service->login($input);
        } catch (InvalidCredentialsException) {
            return $this->unauthenticated($request, $response);
        }

        return $this->withRefreshCookie(
            $this->responses->success(
                $response,
                $this->mapper->publicAuthentication($authentication),
                $this->requestId($request),
            ),
            $authentication->refreshToken,
        );
    }

    public function refresh(ServerRequestInterface $request, ResponseInterface $response, RefreshTokenRequestDto $input): ResponseInterface
    {
        try {
            $authentication = $this->service->refresh($input);
        } catch (InvalidSessionException|UnavailableUserException) {
            return $this->unauthenticated($request, $response);
        }

        return $this->withRefreshCookie(
            $this->responses->success(
                $response,
                $this->mapper->publicAuthentication($authentication),
                $this->requestId($request),
            ),
            $authentication->refreshToken,
        );
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response, LogoutRequestDto $input): ResponseInterface
    {
        try {
            $this->service->logout($input);
        } catch (InvalidSessionException) {
            // Logout is idempotent and never discloses session state.
        }

        return $this->clearRefreshCookie(
            $this->responses->success($response, null, $this->requestId($request), 204),
        );
    }

    public function me(ServerRequestInterface $request, ResponseInterface $response, AccessTokenRequestDto $input): ResponseInterface
    {
        try {
            return $this->responses->success($response, $this->service->currentUser($input), $this->requestId($request));
        } catch (DomainException|UnavailableUserException) {
            return $this->unauthenticated($request, $response);
        }
    }

    private function unauthenticated(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->responses->problem(
            $response,
            'UNAUTHENTICATED',
            'Credenciais invalidas ou expiradas.',
            401,
            $this->requestId($request),
        );
    }

    private function withRefreshCookie(ResponseInterface $response, string $token): ResponseInterface
    {
        return $response->withAddedHeader('Set-Cookie', $this->refreshCookie($token));
    }

    private function clearRefreshCookie(ResponseInterface $response): ResponseInterface
    {
        return $response->withAddedHeader('Set-Cookie', $this->refreshCookie('', 'Thu, 01 Jan 1970 00:00:00 GMT'));
    }

    private function refreshCookie(string $token, ?string $expires = null): string
    {
        $parts = [
            'refresh_token=' . rawurlencode($token),
            'Path=/api/v1/auth',
            'HttpOnly',
            'SameSite=Lax',
        ];

        if (Database::env('APP_ENV', 'development') === 'production') {
            $parts[] = 'Secure';
        }
        if ($expires !== null) {
            $parts[] = 'Expires=' . $expires;
            $parts[] = 'Max-Age=0';
        }

        return implode('; ', $parts);
    }

    private function requestId(ServerRequestInterface $request): string
    {
        return (string) $request->getAttribute('request_id');
    }
}
