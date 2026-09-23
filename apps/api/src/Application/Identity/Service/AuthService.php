<?php
declare(strict_types=1);

namespace App\Application\Identity\Service;

use App\Application\Identity\DTO\Request\LoginRequestDto;
use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
use App\Application\Identity\DTO\Request\LogoutRequestDto;
use App\Application\Identity\DTO\Request\RefreshTokenRequestDto;
use App\Application\Identity\DTO\Response\AuthenticationResponseDto;
use App\Application\Identity\DTO\Response\CurrentUserResponseDto;
use App\Application\Identity\Mapper\IdentityResponseMapper;
use App\Domain\Identity\Entity\AuthEvent;
use App\Domain\Identity\Entity\AuthSession;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Exception\InvalidCredentialsException;
use App\Domain\Identity\Exception\InvalidSessionException;
use App\Domain\Identity\Exception\UnavailableUserException;
use App\Domain\Identity\Repository\AuthEventRepositoryInterface;
use App\Domain\Identity\Repository\AuthSessionRepositoryInterface;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use App\Domain\Identity\ValueObject\RefreshToken;
use App\Application\Identity\Port\AccessTokenIssuerInterface;
use App\Application\Identity\Port\AccessTokenVerifierInterface;
use App\Application\Identity\Port\ClockInterface;
use App\Application\Identity\Port\IpAddressHasherInterface;
use App\Application\Identity\Port\PasswordHasherInterface;
use App\Application\Identity\Port\RefreshTokenGeneratorInterface;
use App\Application\Identity\Port\TransactionManagerInterface;

/** Application service for Identity & Access use cases. */
final class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly AuthSessionRepositoryInterface $sessions,
        private readonly AuthEventRepositoryInterface $events,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly AccessTokenIssuerInterface $accessTokens,
        private readonly AccessTokenVerifierInterface $accessTokenVerifier,
        private readonly RefreshTokenGeneratorInterface $refreshTokens,
        private readonly IpAddressHasherInterface $ipAddresses,
        private readonly ClockInterface $clock,
        private readonly TransactionManagerInterface $transactions,
        private readonly IdentityResponseMapper $mapper,
        private readonly int $refreshTokenTtlSeconds,
    ) {
        if ($refreshTokenTtlSeconds < 1) { throw new \InvalidArgumentException('The refresh-token TTL must be positive.'); }
    }

    public function login(LoginRequestDto $request): AuthenticationResponseDto
    {
        $user = $this->users->findByEmail(mb_strtolower(trim($request->email)));
        if ($user === null || !$user->isActive() || !$this->passwordHasher->verify($request->password, $user->passwordHash)) {
            $this->events->append(AuthEvent::loginFailed($user?->id, $this->ipAddresses->hash($request->ipAddress), $this->clock->now()));
            throw new InvalidCredentialsException();
        }
        return $this->transactions->transactional(function () use ($user, $request): AuthenticationResponseDto {
            $this->events->append(AuthEvent::loginSucceeded($user->id, $this->ipAddresses->hash($request->ipAddress), $this->clock->now()));
            return $this->issueSession($user, null, $request->deviceName);
        });
    }

    public function refresh(RefreshTokenRequestDto $request): AuthenticationResponseDto
    {
        $token = RefreshToken::fromPlainText($request->refreshToken);
        return $this->transactions->transactional(function () use ($token, $request): AuthenticationResponseDto {
            $session = $this->sessions->findByRefreshTokenHashForUpdate($token->hash());
            if ($session === null) { throw new InvalidSessionException(); }
            if ($session->isRevoked()) {
                $this->sessions->revokeFamily($session->familyId, $this->clock->now());
                throw new InvalidSessionException();
            }
            if ($session->isExpiredAt($this->clock->now())) {
                $this->sessions->revoke($session->id, $this->clock->now());
                throw new InvalidSessionException();
            }
            $user = $this->users->findById($session->userId);
            if ($user === null || !$user->isActive()) {
                $this->sessions->revokeFamily($session->familyId, $this->clock->now());
                throw new UnavailableUserException();
            }
            $this->sessions->revoke($session->id, $this->clock->now());
            return $this->issueSession($user, $session->familyId, $request->deviceName ?? $session->deviceName);
        });
    }

    public function logout(LogoutRequestDto $request): void
    {
        $token = RefreshToken::fromPlainText($request->refreshToken);
        $this->transactions->transactional(function () use ($token): void {
            $session = $this->sessions->findByRefreshTokenHashForUpdate($token->hash());
            if ($session !== null && !$session->isRevoked()) { $this->sessions->revoke($session->id, $this->clock->now()); }
        });
    }

    public function currentUser(AccessTokenRequestDto $request): CurrentUserResponseDto
    {
        $user = $this->users->findById($this->accessTokenVerifier->verify($request->accessToken)->subject);
        if ($user === null || !$user->isActive()) { throw new UnavailableUserException(); }
        return $this->mapper->currentUser($user);
    }

    private function issueSession(User $user, ?string $familyId, ?string $deviceName): AuthenticationResponseDto
    {
        $refreshToken = $this->refreshTokens->generate();
        $this->sessions->save(AuthSession::create($user->id, RefreshToken::fromPlainText($refreshToken)->hash(), $this->clock->now()->modify(sprintf('+%d seconds', $this->refreshTokenTtlSeconds)), $familyId, $deviceName));
        return $this->mapper->authentication($this->accessTokens->issue($user), $refreshToken, $user);
    }
}
