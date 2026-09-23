<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Repository;

use App\Domain\QuestionBank\Entity\QuestionImport;

interface QuestionImportRepositoryInterface
{
    public function save(QuestionImport $import): void;

    public function findByIdForUser(string $id, string $userId): ?QuestionImport;
}
