<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Service;

use App\Application\QuestionBank\DTO\Response\ImportRowValidationResponseDto;
use App\Application\QuestionBank\DTO\Response\ImportValidationReportResponseDto;
use App\Domain\QuestionBank\Repository\QuestionImportRepositoryInterface;

final class GetQuestionImportService
{
    public function __construct(private readonly QuestionImportRepositoryInterface $imports)
    {
    }

    public function getForUser(string $id, string $userId): ?ImportValidationReportResponseDto
    {
        $import = $this->imports->findByIdForUser($id, $userId);
        if ($import === null) {
            return null;
        }

        return new ImportValidationReportResponseDto(
            $import->validRows,
            $import->invalidRows,
            array_map(
                static fn ($row): ImportRowValidationResponseDto => new ImportRowValidationResponseDto($row->lineNumber, $row->valid, $row->errors),
                $import->rows,
            ),
            $import->id,
        );
    }
}
