<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionBank;

use App\Application\QuestionBank\DTO\Request\PreviewQuestionImportRequestDto;
use App\Application\QuestionBank\Service\PreviewQuestionImportService;
use App\Application\QuestionBank\Service\QuestionImportValidationService;
use App\Infrastructure\Import\JsonCsvQuestionImportReader;
use PHPUnit\Framework\TestCase;

final class PreviewQuestionImportServiceTest extends TestCase
{
    public function testItPreviewsJsonWithoutPersistingRows(): void
    {
        $service = new PreviewQuestionImportService(new JsonCsvQuestionImportReader(), new QuestionImportValidationService());

        $report = $service->preview(new PreviewQuestionImportRequestDto('json', '[{"statement":"Questao","options":[{"id":"a"},{"id":"b"}],"correct_option":"a"}]'));

        self::assertSame(1, $report->validRows);
        self::assertSame(0, $report->invalidRows);
    }

    public function testItReportsInvalidCsvRow(): void
    {
        $service = new PreviewQuestionImportService(new JsonCsvQuestionImportReader(), new QuestionImportValidationService());
        $csv = "statement,options,correct_option\nQuestao,not-json,a\n";

        $report = $service->preview(new PreviewQuestionImportRequestDto('csv', $csv));

        self::assertSame(0, $report->validRows);
        self::assertSame(1, $report->invalidRows);
    }
}
