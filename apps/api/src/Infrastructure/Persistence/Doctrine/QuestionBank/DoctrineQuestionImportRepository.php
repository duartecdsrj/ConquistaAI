<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Domain\QuestionBank\Entity\QuestionImport;
use App\Domain\QuestionBank\Entity\QuestionImportRow;
use App\Domain\QuestionBank\Repository\QuestionImportRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionImportRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionImportRowRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineQuestionImportRepository implements QuestionImportRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(QuestionImport $import): void
    {
        $record = new QuestionImportRecord();
        $record->id = $import->id;
        $record->createdBy = $import->createdBy;
        $record->format = $import->format;
        $record->filename = $import->filename;
        $record->status = $import->status;
        $record->totals = ['valid' => $import->validRows, 'invalid' => $import->invalidRows];
        $record->createdAt = $import->createdAt;
        $this->entityManager->persist($record);

        foreach ($import->rows as $row) {
            $rowRecord = new QuestionImportRowRecord();
            $rowRecord->importId = $import->id;
            $rowRecord->lineNumber = $row->lineNumber;
            $rowRecord->payload = $row->payload;
            $rowRecord->status = $row->valid ? 'VALID' : 'INVALID';
            $rowRecord->errors = $row->errors;
            $this->entityManager->persist($rowRecord);
        }
    }

    public function findByIdForUser(string $id, string $userId): ?QuestionImport
    {
        $record = $this->entityManager->createQueryBuilder()
            ->select('import')
            ->from(QuestionImportRecord::class, 'import')
            ->where('import.id = :id')
            ->andWhere('import.createdBy = :userId')
            ->setParameter('id', $id)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$record instanceof QuestionImportRecord) {
            return null;
        }

        $rows = $this->entityManager->createQueryBuilder()
            ->select('row')
            ->from(QuestionImportRowRecord::class, 'row')
            ->where('row.importId = :importId')
            ->setParameter('importId', $record->id)
            ->orderBy('row.lineNumber', 'ASC')
            ->getQuery()
            ->getResult();

        return new QuestionImport(
            $record->id,
            $record->createdBy,
            $record->format,
            $record->filename,
            $record->status,
            (int) ($record->totals['valid'] ?? 0),
            (int) ($record->totals['invalid'] ?? 0),
            array_map(static fn (QuestionImportRowRecord $row): QuestionImportRow => new QuestionImportRow($row->lineNumber, $row->payload, $row->status === 'VALID', $row->errors), $rows),
            $record->createdAt,
        );
    }
}
