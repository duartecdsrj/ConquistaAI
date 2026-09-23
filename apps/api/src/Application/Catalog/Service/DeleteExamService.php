<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Domain\Catalog\Repository\ExamRepositoryInterface;

final class DeleteExamService
{
    public function __construct(
        private readonly ExamRepositoryInterface $exams,
        private readonly TransactionManagerInterface $transactions,
    ) {
    }

    public function delete(string $id): bool
    {
        return $this->transactions->transactional(fn (): bool => $this->exams->deleteById($id));
    }
}
