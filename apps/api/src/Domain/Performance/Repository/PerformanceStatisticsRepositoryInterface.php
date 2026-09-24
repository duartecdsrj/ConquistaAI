<?php
declare(strict_types=1);

namespace App\Domain\Performance\Repository;

use App\Domain\Performance\ValueObject\CompletedAnswer;
use App\Domain\Performance\ValueObject\SyllabusCompletedAnswer;
use App\Domain\Performance\ValueObject\StudyPlanAnswer;
use App\Domain\Performance\ValueObject\SyllabusOption;
use App\Domain\Performance\ValueObject\TaxonomyHierarchyNode;

interface PerformanceStatisticsRepositoryInterface
{
    /** @return list<CompletedAnswer> */
    public function completedAnswersForUser(string $userId): array;
    /** @return list<SyllabusOption> */
    public function syllabiWithCompletedAnswersForUser(string $userId): array;
    /** @return list<SyllabusCompletedAnswer> */
    public function completedAnswersForUserAndSyllabus(string $userId, string $syllabusId): array;
    /** @return list<TaxonomyHierarchyNode> */
    public function taxonomyHierarchyForSyllabus(string $syllabusId): array;
    /** @return list<StudyPlanAnswer> */
    public function completedPlanAnswersForUser(string $userId): array;
}
