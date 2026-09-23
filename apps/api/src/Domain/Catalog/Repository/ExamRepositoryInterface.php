<?php
declare(strict_types=1);

namespace App\Domain\Catalog\Repository;

use App\Domain\Catalog\Entity\Exam;

interface ExamRepositoryInterface
{
    public function save(Exam $exam): void;

    public function findById(string $id): ?Exam;

    /** @return list<Exam> */
    public function list(): array;
}
