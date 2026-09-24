<?php
declare(strict_types=1);

namespace App\Domain\Catalog\Repository;

interface SubjectTaxonomyAssignmentRepositoryInterface
{
    /** @param list<string> $taxonomySubjectIds */
    public function replaceForSubject(string $subjectId, array $taxonomySubjectIds): void;
    /** @return list<string> */
    public function listTaxonomySubjectIds(string $subjectId): array;
}
