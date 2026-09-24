<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Catalog;

use App\Domain\Catalog\Repository\SubjectTaxonomyAssignmentRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\SubjectTaxonomyAssignmentRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineSubjectTaxonomyAssignmentRepository implements SubjectTaxonomyAssignmentRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}
    public function replaceForSubject(string $subjectId, array $taxonomySubjectIds): void
    {
        $this->entityManager->createQueryBuilder()->delete(SubjectTaxonomyAssignmentRecord::class, 'assignment')->where('assignment.subjectId = :subjectId')->setParameter('subjectId', $subjectId)->getQuery()->execute();
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        foreach (array_values(array_unique($taxonomySubjectIds)) as $taxonomySubjectId) { $record = new SubjectTaxonomyAssignmentRecord(); $record->subjectId = $subjectId; $record->taxonomySubjectId = $taxonomySubjectId; $record->createdAt = $now; $this->entityManager->persist($record); }
    }
    public function listTaxonomySubjectIds(string $subjectId): array
    {
        return array_map(static fn (SubjectTaxonomyAssignmentRecord $record): string => $record->taxonomySubjectId, $this->entityManager->createQueryBuilder()->select('assignment')->from(SubjectTaxonomyAssignmentRecord::class, 'assignment')->where('assignment.subjectId = :subjectId')->setParameter('subjectId', $subjectId)->getQuery()->getResult());
    }
}
