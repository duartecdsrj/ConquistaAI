<?php
declare(strict_types=1);

namespace Tests\Unit\Identity;

use App\Application\Identity\DTO\Request\RefreshTokenRequestDto;
use App\Application\Identity\Mapper\IdentityResponseMapper;
use App\Application\Identity\Port\AccessTokenClaims;
use App\Application\Identity\Port\AccessTokenIssuerInterface;
use App\Application\Identity\Port\AccessTokenVerifierInterface;
use App\Application\Identity\Port\ClockInterface;
use App\Application\Identity\Port\IpAddressHasherInterface;
use App\Application\Identity\Port\PasswordHasherInterface;
use App\Application\Identity\Port\RefreshTokenGeneratorInterface;
use App\Application\Identity\Port\TransactionManagerInterface;
use App\Application\Identity\Service\AuthService;
use App\Domain\Identity\Entity\AuthEvent;
use App\Domain\Identity\Entity\AuthSession;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Exception\InvalidSessionException;
use App\Domain\Identity\Repository\AuthEventRepositoryInterface;
use App\Domain\Identity\Repository\AuthSessionRepositoryInterface;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class AuthServiceTest extends TestCase
{
    public function testReusedRefreshTokenRevokesItsEntireFamily(): void
    {
        $oldToken = 'old-token';
        $activeToken = 'active-token';
        $familyId = 'family-id';
        $old = new AuthSession('old', 'user-id', $familyId, hash('sha256', $oldToken), new \DateTimeImmutable('+1 hour'), new \DateTimeImmutable('-1 minute'), null);
        $active = new AuthSession('active', 'user-id', $familyId, hash('sha256', $activeToken), new \DateTimeImmutable('+1 hour'), null, null);
        $sessions = new InMemoryAuthSessions([$old, $active]);

        $service = new AuthService(
            new InMemoryUsers(),
            $sessions,
            new InMemoryEvents(),
            new TestPasswordHasher(),
            new TestTokens(),
            new TestTokens(),
            new TestRefreshTokens(),
            new TestIpHasher(),
            new TestClock(),
            new InlineTransactionManager(),
            new IdentityResponseMapper(),
            3600,
        );

        try {
            $service->refresh(new RefreshTokenRequestDto($oldToken));
            self::fail('A reutilizacao de token deveria ser recusada.');
        } catch (InvalidSessionException) {
            self::assertTrue($sessions->isRevoked('active'));
        }
    }
}

final class InMemoryUsers implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User { return null; }
    public function findById(string $id): ?User { return null; }
    public function save(User $user): void {}
}
final class InMemoryAuthSessions implements AuthSessionRepositoryInterface
{
    /** @var array<string, AuthSession> */
    private array $sessions = [];
    /** @param list<AuthSession> $sessions */
    public function __construct(array $sessions) { foreach ($sessions as $session) { $this->sessions[$session->id] = $session; } }
    public function findByRefreshTokenHashForUpdate(string $hash): ?AuthSession { foreach ($this->sessions as $session) { if ($session->refreshTokenHash === $hash) return $session; } return null; }
    public function save(AuthSession $session): void { $this->sessions[$session->id] = $session; }
    public function revoke(string $id, \DateTimeImmutable $at): void { $session = $this->sessions[$id]; $this->sessions[$id] = new AuthSession($session->id, $session->userId, $session->familyId, $session->refreshTokenHash, $session->expiresAt, $at, $session->deviceName); }
    public function revokeFamily(string $familyId, \DateTimeImmutable $at): void { foreach ($this->sessions as $id => $session) { if ($session->familyId === $familyId && !$session->isRevoked()) $this->revoke($id, $at); } }
    public function isRevoked(string $id): bool { return $this->sessions[$id]->isRevoked(); }
}
final class InMemoryEvents implements AuthEventRepositoryInterface { public function append(AuthEvent $event): void {} }
final class TestPasswordHasher implements PasswordHasherInterface { public function hash(string $plainText): string { return $plainText; } public function verify(string $plainText, string $hash): bool { return true; } }
final class TestTokens implements AccessTokenIssuerInterface, AccessTokenVerifierInterface { public function issue(User $user): string { return 'access-token'; } public function verify(string $accessToken): AccessTokenClaims { return new AccessTokenClaims('user-id'); } }
final class TestRefreshTokens implements RefreshTokenGeneratorInterface { public function generate(): string { return 'new-token'; } }
final class TestIpHasher implements IpAddressHasherInterface { public function hash(string $ipAddress): string { return 'ip-hash'; } }
final class TestClock implements ClockInterface { public function now(): \DateTimeImmutable { return new \DateTimeImmutable('2026-01-01T00:00:00+00:00'); } }
final class InlineTransactionManager implements TransactionManagerInterface { public function transactional(callable $callback): mixed { return $callback(); } }
