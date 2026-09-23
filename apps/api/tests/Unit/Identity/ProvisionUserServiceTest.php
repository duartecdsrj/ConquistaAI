<?php
declare(strict_types=1);

namespace Tests\Unit\Identity;

use App\Application\Identity\DTO\Request\ProvisionUserRequestDto;
use App\Application\Identity\Port\PasswordHasherInterface;
use App\Application\Identity\Port\TransactionManagerInterface;
use App\Application\Identity\Service\ProvisionUserService;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class ProvisionUserServiceTest extends TestCase
{
    public function testItCreatesAnActiveUserWithNormalizedRoles(): void
    {
        $users = new ProvisioningUsers();
        $service = new ProvisionUserService($users, new ProvisioningPasswordHasher(), new ProvisioningTransactionManager());

        $result = $service->provision(new ProvisionUserRequestDto(
            'ADMIN@EXAMPLE.TEST',
            'Administrador',
            'senha-segura-123',
            ['admin', 'user', 'admin'],
        ));

        self::assertSame('admin@example.test', $result->email);
        self::assertSame(['ADMIN', 'USER'], $result->roles);
        self::assertSame('hashed:senha-segura-123', $users->saved?->passwordHash);
    }
}
final class ProvisioningUsers implements UserRepositoryInterface
{
    public ?User $saved = null;
    public function findByEmail(string $email): ?User { return null; }
    public function findById(string $id): ?User { return null; }
    public function save(User $user): void { $this->saved = $user; }
}
final class ProvisioningPasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainText): string { return 'hashed:' . $plainText; }
    public function verify(string $plainText, string $hash): bool { return false; }
}
final class ProvisioningTransactionManager implements TransactionManagerInterface
{
    public function transactional(callable $callback): mixed { return $callback(); }
}
