<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Service;

use App\Application\QuestionBank\DTO\Request\PreviewQuestionImportRequestDto;
use App\Application\QuestionBank\DTO\Response\ImportValidationReportResponseDto;
use App\Application\QuestionBank\Port\QuestionImportReaderInterface;

final class PreviewQuestionImportService
{
    public function __construct(
        private readonly QuestionImportReaderInterface $reader,
        private readonly QuestionImportValidationService $validator,
    ) {
    }

    public function preview(PreviewQuestionImportRequestDto $request): ImportValidationReportResponseDto
    {
        $format = strtoupper(trim($request->format));
        if (!in_array($format, ['JSON', 'CSV'], true)) {
            throw new \InvalidArgumentException('Formato de importacao nao suportado.');
        }

        return $this->validator->validate($this->reader->read($format, $request->content));
    }
}
