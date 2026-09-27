<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionBank;

use App\Application\QuestionBank\Service\QuestionImportValidationService;
use App\Domain\QuestionBank\ValueObject\QuestionStatementFingerprint;
use PHPUnit\Framework\TestCase;

final class QuestionImportValidationServiceTest extends TestCase
{
    public function testItMarksRepeatedStatementsAsDuplicateCandidates(): void
    {
        $report = (new QuestionImportValidationService())->validate([
            ['statement' => 'Pergunta repetida', 'options' => [['id' => 'a'], ['id' => 'b']], 'correct_option' => 'a'],
            ['statement' => ' pergunta   repetida ', 'options' => [['id' => 'a'], ['id' => 'b']], 'correct_option' => 'b'],
        ]);

        self::assertSame(1, $report->validRows);
        self::assertSame(1, $report->invalidRows);
        self::assertSame('DUPLICATE_CANDIDATE', $report->rows[1]->errors[0]['code']);
    }
    public function testItRecognizesExistingStatementWithOnlyPdfLayoutDifferences(): void
    {
        $report = (new QuestionImportValidationService())->validate([
            ['statement' => 'A função é ﬁnal: valor = 10.', 'options' => [['id' => 'a'], ['id' => 'b']], 'correct_option' => 'a'],
        ], [QuestionStatementFingerprint::of('A FUNCAO E FINAL — valor 10') => 'existing-question']);

        self::assertSame(0, $report->validRows);
        self::assertSame('DUPLICATE_CANDIDATE', $report->rows[0]->errors[0]['code']);
    }

}
