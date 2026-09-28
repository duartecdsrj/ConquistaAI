<?php
declare(strict_types=1);

namespace Tests\Unit\Identity;

use App\Application\Identity\DTO\Request\AccessTokenRequestDto;
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
use App\Application\Identity\Service\UserAdministrationService;
use App\Domain\Identity\Entity\AuthEvent;
use App\Domain\Identity\Entity\AuthSession;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Repository\AuthEventRepositoryInterface;
use App\Domain\Identity\Repository\AuthSessionRepositoryInterface;
use App\Domain\Identity\Repository\GoogleIdentityRepositoryInterface;
use App\Domain\Identity\Repository\IdentityAuditEventRepositoryInterface;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use App\Infrastructure\Http\ApiResponseFactory;
use App\Interface\Http\Identity\Controller\UserAdministrationController;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class UserAdministrationControllerTest extends TestCase
{
    public function testNonAdministratorCannotListUsers(): void
    {
        $user = new User('user', 'user@example.test', 'Usuário', null, 'ACTIVE', ['USER']);
        $users = new ControllerUsers($user);
        $controller = new UserAdministrationController($this->auth($users), $this->administration($users), new ApiResponseFactory());
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/v1/admin/users')->withAttribute('request_id', 'request-1');

        $response = $controller->list($request, (new ResponseFactory())->createResponse(), new AccessTokenRequestDto('token'));
        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(403, $response->getStatusCode());
        self::assertSame('FORBIDDEN', $payload['error']['code']);
        self::assertSame('request-1', $payload['meta']['request_id']);
    }

    private function auth(ControllerUsers $users): AuthService
    {
        $tokens = new class implements AccessTokenIssuerInterface, AccessTokenVerifierInterface {
            public function issue(User $user): string { return 'token'; }
            public function verify(string $accessToken): AccessTokenClaims { return new AccessTokenClaims('user'); }
        };
        return new AuthService($users, new class implements AuthSessionRepositoryInterface { public function findByRefreshTokenHashForUpdate(string $hash): ?AuthSession { return null; } public function save(AuthSession $session): void {} public function revoke(string $id, \DateTimeImmutable $at): void {} public function revokeFamily(string $familyId, \DateTimeImmutable $at): void {} }, new class implements AuthEventRepositoryInterface { public function append(AuthEvent $event): void {} }, new class implements PasswordHasherInterface { public function hash(string $plainText): string { return $plainText; } public function verify(string $plainText, string $hash): bool { return false; } }, $tokens, $tokens, new class implements RefreshTokenGeneratorInterface { public function generate(): string { return 'refresh'; } }, new class implements IpAddressHasherInterface { public function hash(string $ipAddress): string { return 'ip'; } }, new class implements ClockInterface { public function now(): \DateTimeImmutable { return new \DateTimeImmutable(); } }, new class implements TransactionManagerInterface { public function transactional(callable $callback): mixed { return $callback(); } }, new IdentityResponseMapper(), 3600);
    }

    private function administration(ControllerUsers $users): UserAdministrationService
    {
        return new UserAdministrationService($users, new class implements GoogleIdentityRepositoryInterface { public function findByEmail(\App\Domain\Identity\ValueObject\GoogleEmail $email): ?\App\Domain\Identity\Entity\GoogleIdentity { return null; } public function findByUserId(string $userId): ?\App\Domain\Identity\Entity\GoogleIdentity { return null; } public function save(\App\Domain\Identity\Entity\GoogleIdentity $identity): void {} public function removeForUserId(string $userId): void {} }, new class implements IdentityAuditEventRepositoryInterface { public function append(\App\Domain\Identity\Entity\IdentityAuditEvent $event): void {} }, new class implements PasswordHasherInterface { public function hash(string $plainText): string { return $plainText; } public function verify(string $plainText, string $hash): bool { return false; } }, new class implements ClockInterface { public function now(): \DateTimeImmutable { return new \DateTimeImmutable(); } }, new class implements TransactionManagerInterface { public function transactional(callable $callback): mixed { return $callback(); } });
    }
}

final class ControllerUsers implements UserRepositoryInterface
{
    public function __construct(private readonly User $user) {}
    public function findByEmail(string $email): ?User { return $this->user->email === $email ? $this->user : null; }
    public function findById(string $id): ?User { return $this->user->id === $id ? $this->user : null; }
    public function save(User $user): void {}
    public function list(int $offset, int $limit, ?string $query, ?string $status): array { return []; }
    public function count(?string $query, ?string $status): int { return 0; }
    public function countActiveAdmins(): int { return 0; }
}
