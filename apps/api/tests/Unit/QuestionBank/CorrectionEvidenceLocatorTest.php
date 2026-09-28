<?php
declare(strict_types=1);

namespace App\Tests\Unit\QuestionBank;

use App\Infrastructure\QuestionBank\CorrectionEvidenceLocator;
use PHPUnit\Framework\TestCase;

final class CorrectionEvidenceLocatorTest extends TestCase
{
    public function testSelectsTheLongestLineAfterRemovingOnlyEdgeSpecialCharacters(): void
    {
        $references = CorrectionEvidenceLocator::automaticReferences([
            "statement" => "*** curta ***\n((Linha com o maior conteúdo: mantém - separador interno!))\n<menor>",
            "options" => [["content" => "Uma alternativa longa que não pode ser pesquisada automaticamente."]],
        ]);

        self::assertSame(["statement_longest_line" => "Linha com o maior conteúdo: mantém - separador interno"], $references);
    }

    public function testKeepsTheFirstLongestLineWhenLengthsTie(): void
    {
        self::assertSame(["statement_longest_line" => "empate"], CorrectionEvidenceLocator::automaticReferences([
            "statement" => "## empate ##\n@@ empate @@",
        ]));
    }

    public function testReturnsNoReferenceForLinesWithoutUsefulContent(): void
    {
        self::assertSame([], CorrectionEvidenceLocator::automaticReferences([
            "statement" => "  ***  \n---\n\t",
            "options" => [["content" => "Alternativa não deve ser usada"]],
        ]));
    }

    public function testKeepsExistingEvidenceWhenTheAutomaticSearchDoesNotMatch(): void
    {
        self::assertSame([1, 2, 3], CorrectionEvidenceLocator::mergeEvidenceWindows([1, 2, 3], []));
    }
}
