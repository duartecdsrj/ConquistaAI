<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\Repository;

interface TaxonomyHierarchyRepositoryInterface
{
    /** @return list<string> IDs dos ancestrais, do pai até a raiz. */
    public function ancestorIds(string $subjectId): array;
}
