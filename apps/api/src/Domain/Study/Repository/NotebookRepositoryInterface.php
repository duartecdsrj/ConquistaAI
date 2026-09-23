<?php

declare(strict_types=1);

namespace App\Domain\Study\Repository;

use App\Domain\Study\Entity\Notebook;

interface NotebookRepositoryInterface
{
    public function save(Notebook $notebook): void;
    public function findByIdForUser(string $id, string $userId): ?Notebook;
}
