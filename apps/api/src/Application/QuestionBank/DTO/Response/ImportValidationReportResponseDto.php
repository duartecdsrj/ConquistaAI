<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
final readonly class ImportValidationReportResponseDto {
    /** @param list<ImportRowValidationResponseDto> $rows */
    public function __construct(public int $validRows,public int $invalidRows,public array $rows) {}
}
