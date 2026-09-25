<?php
declare(strict_types=1);

namespace App\Domain\Catalog\Repository;

interface PositionTaxonomyAssignmentRepositoryInterface
{
    /** @param list<string> $taxonomySubjectIds */
    public function replaceForPosition(string $positionId, array $taxonomySubjectIds): void;

    /** @return list<string> */
    public function listTaxonomySubjectIds(string $positionId): array;
}
