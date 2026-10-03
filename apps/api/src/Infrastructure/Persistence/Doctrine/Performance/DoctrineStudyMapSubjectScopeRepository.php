<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance;

use App\Domain\Performance\Repository\StudyMapSubjectScopeRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\PositionRecord;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\PositionTaxonomyAssignmentRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineStudyMapSubjectScopeRepository implements StudyMapSubjectScopeRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function containsSubject(string $examId, string $subjectId): bool
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(assignment.taxonomySubjectId)')
            ->from(PositionTaxonomyAssignmentRecord::class, 'assignment')
            ->innerJoin(PositionRecord::class, 'position', 'WITH', 'position.id = assignment.positionId')
            ->where('position.examId = :examId')
            ->andWhere('assignment.taxonomySubjectId = :subjectId')
            ->setParameter('examId', $examId)
            ->setParameter('subjectId', $subjectId)
            ->getQuery()->getSingleScalarResult() > 0;
    }
}
