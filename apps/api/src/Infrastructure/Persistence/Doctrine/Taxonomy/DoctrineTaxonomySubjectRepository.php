<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Taxonomy;

use App\Domain\Taxonomy\Entity\TaxonomySubject;
use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\Entity\TaxonomySubjectRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTaxonomySubjectRepository implements TaxonomySubjectRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(TaxonomySubject $subject): void
    {
        $record = $this->entityManager->find(TaxonomySubjectRecord::class, $subject->id);
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        if (!$record instanceof TaxonomySubjectRecord) {
            $record = new TaxonomySubjectRecord();
            $record->id = $subject->id;
            $record->createdAt = $now;
            $this->entityManager->persist($record);
        }

        $record->parentId = $subject->parentId;
        $record->name = $subject->name;
        $record->slug = $subject->slug;
        $record->description = $subject->description;
        $record->level = $subject->level;
        $record->active = $subject->active;
        $record->updatedAt = $now;
    }

    public function findById(string $id): ?TaxonomySubject
    {
        $record = $this->entityManager->find(TaxonomySubjectRecord::class, $id);

        return $record instanceof TaxonomySubjectRecord ? $this->map($record) : null;
    }

    public function findByParentAndSlug(?string $parentId, string $slug): ?TaxonomySubject
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('subject')
            ->from(TaxonomySubjectRecord::class, 'subject')
            ->where('subject.slug = :slug')
            ->setParameter('slug', $slug);

        $parentId === null
            ? $query->andWhere('subject.parentId IS NULL')
            : $query->andWhere('subject.parentId = :parentId')->setParameter('parentId', $parentId);

        $record = $query->getQuery()->getOneOrNullResult();

        return $record instanceof TaxonomySubjectRecord ? $this->map($record) : null;
    }

    public function ancestorIds(string $subjectId): array
    {
        $ancestors = [];
        $current = $this->entityManager->find(TaxonomySubjectRecord::class, $subjectId);
        while ($current instanceof TaxonomySubjectRecord && $current->parentId !== null) {
            $ancestors[] = $current->parentId;
            $current = $this->entityManager->find(TaxonomySubjectRecord::class, $current->parentId);
        }

        return $ancestors;
    }

    private function map(TaxonomySubjectRecord $record): TaxonomySubject
    {
        return new TaxonomySubject($record->id, $record->parentId, $record->name, $record->slug, $record->description, $record->level, $record->active);
    }
}
