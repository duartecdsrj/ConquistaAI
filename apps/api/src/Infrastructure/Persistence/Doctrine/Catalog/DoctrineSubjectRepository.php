<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Catalog;

use App\Domain\Catalog\Entity\Subject;
use App\Domain\Catalog\Repository\SubjectRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\SubjectRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineSubjectRepository implements SubjectRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function save(Subject $subject): void
    {
        $record = new SubjectRecord();
        $record->id = $subject->id;
        $record->syllabusId = $subject->syllabusId;
        $record->parentId = $subject->parentId;
        $record->name = $subject->name;
        $record->sortOrder = $subject->sortOrder;
        $record->selectionWeight = number_format($subject->selectionWeight, 4, '.', '');
        $record->weightSource = $subject->weightSource;
        $record->weightConfidence = number_format($subject->weightConfidence, 4, '.', '');
        $record->weightCalculatedAt = $subject->weightCalculatedAt === null ? null : new \DateTimeImmutable($subject->weightCalculatedAt);
        $record->sourceExcerpt = $subject->sourceExcerpt;
        $record->sourcePage = $subject->sourcePage;
        $record->sourceStartOffset = $subject->sourceStartOffset;
        $record->sourceEndOffset = $subject->sourceEndOffset;
        $record->createdAt = new \DateTimeImmutable('now');
        $record->updatedAt = $record->createdAt;
        $this->em->persist($record);
    }

    public function exists(string $id): bool { return (bool) $this->em->createQueryBuilder()->select('COUNT(subject.id)')->from(SubjectRecord::class, 'subject')->where('subject.id = :id')->setParameter('id', $id)->getQuery()->getSingleScalarResult(); }

    public function existsForSyllabus(string $id, string $syllabusId): bool
    {
        return (bool) $this->em->createQueryBuilder()->select('COUNT(subject.id)')->from(SubjectRecord::class, 'subject')->where('subject.id=:id')->andWhere('subject.syllabusId=:syllabusId')->setParameter('id', $id)->setParameter('syllabusId', $syllabusId)->getQuery()->getSingleScalarResult();
    }

    public function listForSyllabus(string $syllabusId): array
    {
        return array_map(static fn (SubjectRecord $record): Subject => new Subject($record->id, $record->syllabusId, $record->parentId, $record->name, $record->sortOrder, $record->sourceExcerpt, $record->sourcePage, $record->sourceStartOffset, $record->sourceEndOffset, (float) $record->selectionWeight, $record->weightSource, (float) $record->weightConfidence, $record->weightCalculatedAt?->format(DATE_ATOM)), $this->em->createQueryBuilder()->select('subject')->from(SubjectRecord::class, 'subject')->where('subject.syllabusId=:id')->setParameter('id', $syllabusId)->orderBy('subject.sortOrder', 'ASC')->addOrderBy('subject.name', 'ASC')->getQuery()->getResult());
    }
}
