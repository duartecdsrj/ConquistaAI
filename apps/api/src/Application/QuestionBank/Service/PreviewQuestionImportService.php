<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Service;

use App\Application\QuestionBank\DTO\Request\PreviewQuestionImportRequestDto;
use App\Application\QuestionBank\DTO\Response\ImportValidationReportResponseDto;
use App\Application\QuestionBank\Port\QuestionImportReaderInterface;
use App\Application\QuestionBank\Port\TransactionManagerInterface;
use App\Domain\QuestionBank\Entity\QuestionImport;
use App\Domain\QuestionBank\Entity\QuestionImportRow;
use App\Domain\QuestionBank\Repository\QuestionImportRepositoryInterface;

final class PreviewQuestionImportService
{
    public function __construct(
        private readonly QuestionImportReaderInterface $reader,
        private readonly QuestionImportValidationService $validator,
        private readonly QuestionImportRepositoryInterface $imports,
        private readonly TransactionManagerInterface $transactions,
        private readonly \DateTimeZone $utc = new \DateTimeZone('UTC'),
    ) {
    }

    public function preview(PreviewQuestionImportRequestDto $request): ImportValidationReportResponseDto
    {
        $format = strtoupper(trim($request->format));
        if (!in_array($format, ['JSON', 'CSV'], true)) {
            throw new \InvalidArgumentException('Formato de importacao nao suportado.');
        }
        if ($request->userId === '') {
            throw new \InvalidArgumentException('Usuario da importacao e obrigatorio.');
        }

        $rows = $this->reader->read($format, $request->content);
        $report = $this->validator->validate($rows);
        $importId = $this->uuid();

        $this->transactions->transactional(function () use ($request, $format, $rows, $report, $importId): void {
            $validationByLine = [];
            foreach ($report->rows as $validation) {
                $validationByLine[$validation->rowNumber] = $validation;
            }

            $this->imports->save(new QuestionImport(
                $importId,
                $request->userId,
                $format,
                strtolower($format) === 'json' ? 'import.json' : 'import.csv',
                'VALIDATED',
                $report->validRows,
                $report->invalidRows,
                array_map(
                    static fn (array $payload, int $index): QuestionImportRow => new QuestionImportRow(
                        $index + 1,
                        $payload,
                        $validationByLine[$index + 1]->valid,
                        $validationByLine[$index + 1]->errors,
                    ),
                    $rows,
                    array_keys($rows),
                ),
                new \DateTimeImmutable('now', $this->utc),
            ));
        });

        return new ImportValidationReportResponseDto($report->validRows, $report->invalidRows, $report->rows, $importId);
    }

    private function uuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
