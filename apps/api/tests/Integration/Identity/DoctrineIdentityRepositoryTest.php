<?php
declare(strict_types=1);

namespace Tests\Integration\Identity;

use App\Domain\Identity\Entity\GoogleIdentity;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\ValueObject\GoogleEmail;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\Identity\DoctrineGoogleIdentityRepository;
use App\Infrastructure\Persistence\Doctrine\Identity\DoctrineUserRepository;
use PHPUnit\Framework\TestCase;

final class DoctrineIdentityRepositoryTest extends TestCase
{
    public function testFindsGoogleIdentityByNonPrimaryEmailAndCountsUserOnlyOnce(): void
    {
        $entityManager = DoctrineEntityManagerFactory::create();
        $connection = $entityManager->getConnection();
        $suffix = bin2hex(random_bytes(8));
        $userId = sprintf('00000000-0000-4000-8000-%012s', substr($suffix, 0, 12));
        $email = 'integration-'.$suffix.'@example.test';
        $now = new \DateTimeImmutable('now');
        $users = new DoctrineUserRepository($entityManager);
        $identities = new DoctrineGoogleIdentityRepository($entityManager);

        $connection->beginTransaction();
        try {
            $users->save(new User($userId, $email, 'Usuário de integração', null, 'ACTIVE', ['USER', 'ADMIN']));
            $identities->save(new GoogleIdentity($userId, GoogleEmail::from($email), $now, $now));
            $entityManager->flush();
            $entityManager->clear();

            $identity = $identities->findByEmail(GoogleEmail::from($email));

            self::assertNotNull($identity);
            self::assertSame($userId, $identity->userId);
            self::assertSame(1, $users->count($email, 'ACTIVE'));
            self::assertSame($userId, $users->list(0, 25, $email, 'ACTIVE')[0]->id);
        } finally {
            $connection->rollBack();
            $entityManager->clear();
        }
    }
}
