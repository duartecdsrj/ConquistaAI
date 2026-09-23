<?php
declare(strict_types=1);

namespace App\Domain\Catalog\Repository;

use App\Domain\Catalog\Entity\Exam;

interface ExamRepositoryInterface
{
    public function save(Exam $exam): void;

    public function findById(string $id): ?Exam;

    public function update(Exam $exam): bool;

    public function deleteById(string $id): bool;

    /** @return list<Exam> */
    public function list(): array;
}
