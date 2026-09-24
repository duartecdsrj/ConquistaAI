<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Repository;

interface QuestionDuplicateDetectorInterface
{
    /** @param list<string> $statements @return array<string, string> normalized statement => question id */
    public function findExistingByStatements(array $statements): array;
}
