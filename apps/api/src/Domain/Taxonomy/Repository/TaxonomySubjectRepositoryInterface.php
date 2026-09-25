<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\Repository;

use App\Domain\Taxonomy\Entity\TaxonomySubject;

interface TaxonomySubjectRepositoryInterface extends TaxonomyHierarchyRepositoryInterface
{
    public function save(TaxonomySubject $subject): void;

    public function findById(string $id): ?TaxonomySubject;

    public function findByParentAndSlug(?string $parentId, string $slug): ?TaxonomySubject;

    /** Reutiliza assunto canônico independentemente da origem editorial. */
    public function findBySlug(string $slug): ?TaxonomySubject;

    /** Localiza equivalência semântica simples por prefixo de slug. */
    public function findByComparableSlug(string $slug): ?TaxonomySubject;

    /** @return list<TaxonomySubject> */
    public function list(int $offset, int $limit): array;

    public function hasChildren(string $subjectId): bool;
    public function count(): int;
}
