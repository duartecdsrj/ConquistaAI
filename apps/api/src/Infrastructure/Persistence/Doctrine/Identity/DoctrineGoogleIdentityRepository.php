<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Identity;
use App\Domain\Identity\Entity\GoogleIdentity;
use App\Domain\Identity\Repository\GoogleIdentityRepositoryInterface;
use App\Domain\Identity\ValueObject\GoogleEmail;
use App\Infrastructure\Persistence\Doctrine\Identity\Entity\GoogleIdentityRecord;
use Doctrine\ORM\EntityManagerInterface;
final class DoctrineGoogleIdentityRepository implements GoogleIdentityRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}
    public function findByEmail(GoogleEmail $email): ?GoogleIdentity
    {
        /** @var GoogleIdentityRecord|null $record */
        $record = $this->entityManager->createQueryBuilder()
            ->select('identity')
            ->from(GoogleIdentityRecord::class, 'identity')
            ->where('identity.googleEmail = :email')
            ->setParameter('email', $email->value)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->map($record);
    }
    public function findByUserId(string $userId): ?GoogleIdentity { return $this->map($this->entityManager->find(GoogleIdentityRecord::class, $userId)); }
    public function save(GoogleIdentity $identity): void { $record=$this->entityManager->find(GoogleIdentityRecord::class,$identity->userId) ?? new GoogleIdentityRecord();$record->userId=$identity->userId;$record->googleEmail=$identity->email->value;$record->createdAt=$identity->createdAt;$record->updatedAt=$identity->updatedAt;$this->entityManager->persist($record); }
    public function removeForUserId(string $userId): void { $record=$this->entityManager->find(GoogleIdentityRecord::class,$userId);if($record instanceof GoogleIdentityRecord)$this->entityManager->remove($record); }
    private function map(?GoogleIdentityRecord $record): ?GoogleIdentity { return $record===null?null:new GoogleIdentity($record->userId,GoogleEmail::from($record->googleEmail),$record->createdAt,$record->updatedAt); }
}
