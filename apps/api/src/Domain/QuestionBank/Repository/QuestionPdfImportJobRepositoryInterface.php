<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Repository;
use App\Domain\QuestionBank\Entity\QuestionPdfImportJob;
interface QuestionPdfImportJobRepositoryInterface
{
    public function save(QuestionPdfImportJob $job): void;
    public function findByIdForUser(string $id, string $userId): ?QuestionPdfImportJob;
    /** @return list<QuestionPdfImportJob> */
    public function listForUser(string $userId, int $offset, int $limit): array;
    public function countForUser(string $userId): int; public function cancelForUser(string $id, string $userId): bool; public function isCancelled(string $id): bool;
    public function claimNext(): ?QuestionPdfImportJob;
}
