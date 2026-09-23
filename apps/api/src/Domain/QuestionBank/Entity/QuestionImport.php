<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Entity;

final readonly class QuestionImport
{
    /** @param list<QuestionImportRow> $rows */
    public function __construct(
        public string $id,
        public string $createdBy,
        public string $format,
        public string $filename,
        public string $status,
        public int $validRows,
        public int $invalidRows,
        public array $rows,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
