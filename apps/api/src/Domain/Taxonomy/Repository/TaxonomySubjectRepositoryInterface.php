<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\Repository;

use App\Domain\Taxonomy\Entity\TaxonomySubject;

interface TaxonomySubjectRepositoryInterface extends TaxonomyHierarchyRepositoryInterface
{
    public function save(TaxonomySubject $subject): void;

    public function findById(string $id): ?TaxonomySubject;

    public function findByParentAndSlug(?string $parentId, string $slug): ?TaxonomySubject;
}
