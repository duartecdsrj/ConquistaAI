<?php
declare(strict_types=1);

namespace App\Domain\Arena\Repository;

interface DuelQuestionSelectorInterface
{
    /** @param list<string> $taxonomySubjectIds @param list<string> $excludedQuestionIds @return list<string> */
    public function selectPublishedBalanced(array $taxonomySubjectIds, array $excludedQuestionIds, int $count): array;
}
