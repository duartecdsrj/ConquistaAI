<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance;

use App\Domain\Performance\Repository\StudyMapSubjectScopeRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\PositionRecord;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\PositionTaxonomyAssignmentRecord;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\Entity\TaxonomySubjectRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineStudyMapSubjectScopeRepository implements StudyMapSubjectScopeRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function containsSubject(string $examId, string $subjectId): bool
    {
        $assignmentRows = $this->entityManager->createQueryBuilder()
            ->select('assignment.taxonomySubjectId AS subjectId')
            ->from(PositionTaxonomyAssignmentRecord::class, 'assignment')
            ->innerJoin(PositionRecord::class, 'position', 'WITH', 'position.id = assignment.positionId')
            ->where('position.examId = :examId')
            ->setParameter('examId', $examId)
            ->getQuery()->getScalarResult();
        $assigned = [];
        foreach ($assignmentRows as $row) {
            $assigned[(string) $row['subjectId']] = true;
        }
        if (isset($assigned[$subjectId])) {
            return true;
        }

        $taxonomyRows = $this->entityManager->createQueryBuilder()
            ->select('taxonomy.id AS id, taxonomy.parentId AS parentId')
            ->from(TaxonomySubjectRecord::class, 'taxonomy')
            ->where('taxonomy.active = true')
            ->getQuery()->getArrayResult();
        $parents = [];
        foreach ($taxonomyRows as $row) {
            $parents[(string) $row['id']] = $row['parentId'] === null ? null : (string) $row['parentId'];
        }
        $visited = [];
        while (isset($parents[$subjectId]) && !isset($visited[$subjectId])) {
            if (isset($assigned[$subjectId])) {
                return true;
            }
            $visited[$subjectId] = true;
            $parentId = $parents[$subjectId];
            if ($parentId === null) {
                break;
            }
            $subjectId = $parentId;
        }

        return isset($assigned[$subjectId]);
    }
}
