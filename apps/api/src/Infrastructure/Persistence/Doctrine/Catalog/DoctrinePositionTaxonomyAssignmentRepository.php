<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Catalog;

use App\Domain\Catalog\Repository\PositionTaxonomyAssignmentRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\PositionTaxonomyAssignmentRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrinePositionTaxonomyAssignmentRepository implements PositionTaxonomyAssignmentRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function replaceForPosition(string $positionId, array $taxonomySubjectIds): void
    {
        $this->entityManager->createQueryBuilder()
            ->delete(PositionTaxonomyAssignmentRecord::class, 'assignment')
            ->where('assignment.positionId = :positionId')
            ->setParameter('positionId', $positionId)
            ->getQuery()->execute();
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        foreach (array_values(array_unique($taxonomySubjectIds)) as $taxonomySubjectId) {
            $record = new PositionTaxonomyAssignmentRecord();
            $record->positionId = $positionId;
            $record->taxonomySubjectId = $taxonomySubjectId;
            $record->createdAt = $now;
            $this->entityManager->persist($record);
        }
    }

    public function listTaxonomySubjectIds(string $positionId): array
    {
        return array_map(
            static fn (PositionTaxonomyAssignmentRecord $record): string => $record->taxonomySubjectId,
            $this->entityManager->createQueryBuilder()->select('assignment')->from(PositionTaxonomyAssignmentRecord::class, 'assignment')->where('assignment.positionId = :positionId')->setParameter('positionId', $positionId)->getQuery()->getResult(),
        );
    }
}
