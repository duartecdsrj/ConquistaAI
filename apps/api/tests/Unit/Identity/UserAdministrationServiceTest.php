<?php
declare(strict_types=1);

namespace Tests\Unit\Identity;

use App\Application\Identity\DTO\Request\CreateAdminUserRequestDto;
use App\Application\Identity\DTO\Request\UpdateUserRolesRequestDto;
use App\Application\Identity\DTO\Request\UpdateUserStatusRequestDto;
use App\Application\Identity\Port\ClockInterface;
use App\Application\Identity\Port\PasswordHasherInterface;
use App\Application\Identity\Port\TransactionManagerInterface;
use App\Application\Identity\Service\UserAdministrationService;
use App\Domain\Identity\Entity\GoogleIdentity;
use App\Domain\Identity\Entity\IdentityAuditEvent;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Repository\GoogleIdentityRepositoryInterface;
use App\Domain\Identity\Repository\IdentityAuditEventRepositoryInterface;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use App\Domain\Identity\ValueObject\GoogleEmail;
use DomainException;
use PHPUnit\Framework\TestCase;

final class UserAdministrationServiceTest extends TestCase
{
    public function testCreatesUserWithReservedGoogleIdentityAndAuditEvent(): void
    {
        $users = new AdministrationUsers();
        $links = new AdministrationGoogleLinks();
        $audit = new AdministrationAuditEvents();
        $result = $this->service($users, $links, $audit)->create(new CreateAdminUserRequestDto('admin', ' Nova Pessoa ', 'NOVA@EXAMPLE.TEST', null, ['USER'], 'PENDING_APPROVAL', 'GOOGLE@EXAMPLE.TEST'));

        self::assertSame('nova@example.test', $result->email);
        self::assertSame('google@example.test', $result->googleEmail);
        self::assertSame('PENDING_APPROVAL', $result->status);
        self::assertCount(2, $audit->events);
        self::assertSame('GOOGLE_IDENTITY_SET', $audit->events[0]->event);
        self::assertSame('USER_CREATED', $audit->events[1]->event);
    }

    public function testRejectsGoogleIdentityAlreadyReservedByAnotherUser(): void
    {
        $users = new AdministrationUsers([
            new User('first', 'first@example.test', 'Primeiro', null, 'ACTIVE', ['USER']),
            new User('second', 'second@example.test', 'Segundo', null, 'ACTIVE', ['USER']),
        ]);
        $links = new AdministrationGoogleLinks([
            new GoogleIdentity('first', GoogleEmail::from('linked@example.test'), new \DateTimeImmutable(), new \DateTimeImmutable()),
        ]);

        $this->expectException(DomainException::class);
        $this->service($users, $links, new AdministrationAuditEvents())->setGoogle('admin', 'second', 'linked@example.test');
    }

    public function testPreventsRemovingAdminRoleFromLastActiveAdministrator(): void
    {
        $users = new AdministrationUsers([
            new User('admin', 'admin@example.test', 'Admin', null, 'ACTIVE', ['ADMIN']),
        ]);

        $this->expectException(DomainException::class);
        $this->service($users, new AdministrationGoogleLinks(), new AdministrationAuditEvents())->roles(new UpdateUserRolesRequestDto('admin', 'admin', ['USER']));
    }

    public function testActivatesPendingUserAndAuditsTheChange(): void
    {
        $users = new AdministrationUsers([
            new User('pending', 'pending@example.test', 'Pendente', null, 'PENDING_APPROVAL', ['USER']),
            new User('admin', 'admin@example.test', 'Admin', null, 'ACTIVE', ['ADMIN']),
        ]);
        $audit = new AdministrationAuditEvents();

        $result = $this->service($users, new AdministrationGoogleLinks(), $audit)->status(new UpdateUserStatusRequestDto('admin', 'pending', 'ACTIVE'));

        self::assertSame('ACTIVE', $result->status);
        self::assertSame('ACTIVE', $users->findById('pending')?->status);
        self::assertSame('USER_STATUS_CHANGED', $audit->events[0]->event);
    }

    public function testListsUsersWithPaginationAndStatusFilter(): void
    {
        $users = new AdministrationUsers([
            new User('active', 'active@example.test', 'Ativo', null, 'ACTIVE', ['USER']),
            new User('pending', 'pending@example.test', 'Pendente', null, 'PENDING_APPROVAL', ['USER']),
        ]);

        $result = $this->service($users, new AdministrationGoogleLinks(), new AdministrationAuditEvents())->list(1, 25, null, 'PENDING_APPROVAL');

        self::assertSame(1, $result['total']);
        self::assertSame('pending', $result['items'][0]->id);
    }

    private function service(AdministrationUsers $users, AdministrationGoogleLinks $links, AdministrationAuditEvents $audit): UserAdministrationService
    {
        return new UserAdministrationService($users, $links, $audit, new AdministrationPasswords(), new AdministrationClock(), new AdministrationTransactions());
    }
}

final class AdministrationUsers implements UserRepositoryInterface
{
    /** @var array<string, User> */
    private array $users = [];

    /** @param list<User> $users */
    public function __construct(array $users = [])
    {
        foreach ($users as $user) {
            $this->users[$user->id] = $user;
        }
    }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->email === $email) {
                return $user;
            }
        }

        return null;
    }

    public function findById(string $id): ?User { return $this->users[$id] ?? null; }
    public function save(User $user): void { $this->users[$user->id] = $user; }
    public function list(int $offset, int $limit, ?string $query, ?string $status): array
    {
        $items = array_values(array_filter($this->users, static fn (User $user): bool => $status === null || $user->status === $status));
        return array_slice($items, $offset, $limit);
    }
    public function count(?string $query, ?string $status): int { return count($this->list(0, PHP_INT_MAX, $query, $status)); }
    public function countActiveAdmins(): int
    {
        return count(array_filter($this->users, static fn (User $user): bool => $user->status === 'ACTIVE' && in_array('ADMIN', $user->roles, true)));
    }
}

final class AdministrationGoogleLinks implements GoogleIdentityRepositoryInterface
{
    /** @var array<string, GoogleIdentity> */
    private array $links = [];

    /** @param list<GoogleIdentity> $links */
    public function __construct(array $links = [])
    {
        foreach ($links as $link) {
            $this->links[$link->email->value] = $link;
        }
    }

    public function findByEmail(GoogleEmail $email): ?GoogleIdentity { return $this->links[$email->value] ?? null; }
    public function findByUserId(string $userId): ?GoogleIdentity
    {
        foreach ($this->links as $link) {
            if ($link->userId === $userId) {
                return $link;
            }
        }
        return null;
    }
    public function save(GoogleIdentity $identity): void
    {
        foreach ($this->links as $email => $link) {
            if ($link->userId === $identity->userId && $email !== $identity->email->value) {
                unset($this->links[$email]);
            }
        }
        $this->links[$identity->email->value] = $identity;
    }
    public function removeForUserId(string $userId): void
    {
        foreach ($this->links as $email => $link) {
            if ($link->userId === $userId) {
                unset($this->links[$email]);
            }
        }
    }
}

final class AdministrationAuditEvents implements IdentityAuditEventRepositoryInterface
{
    /** @var list<IdentityAuditEvent> */
    public array $events = [];
    public function append(IdentityAuditEvent $event): void { $this->events[] = $event; }
}
final class AdministrationPasswords implements PasswordHasherInterface { public function hash(string $plainText): string { return 'hash-'.$plainText; } public function verify(string $plainText, string $hash): bool { return false; } }
final class AdministrationClock implements ClockInterface { public function now(): \DateTimeImmutable { return new \DateTimeImmutable('2026-09-28T12:00:00+00:00'); } }
final class AdministrationTransactions implements TransactionManagerInterface { public function transactional(callable $callback): mixed { return $callback(); } }
