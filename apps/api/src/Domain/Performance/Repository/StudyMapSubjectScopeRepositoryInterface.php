<?php
declare(strict_types=1);

namespace App\Domain\Performance\Repository;

interface StudyMapSubjectScopeRepositoryInterface
{
    public function containsSubject(string $examId, string $subjectId): bool;
}
