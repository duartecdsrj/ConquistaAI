<?php
declare(strict_types=1);
namespace Tests\Unit\QuestionBank;
use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use PHPUnit\Framework\TestCase;
final class ImportedQuestionContentSanitizerTest extends TestCase
{
    public function testRemovesMetadataAndRepeatedAlternatives(): void
    {
        $sanitizer = new ImportedQuestionContentSanitizer();
        $result = $sanitizer->statement(
            'Concurso: Conselho X Cargo: Analista Nível: Superior
No que diz respeito ao tema, assinale.
a) primeira
b) segunda
c) terceira',
            [['content' => 'primeira'], ['content' => 'segunda'], ['content' => 'terceira']],
        );
        self::assertSame('No que diz respeito ao tema, assinale.', $result);
    }
    public function testRemovesInlineAlternativeSequence(): void
    {
        $sanitizer = new ImportedQuestionContentSanitizer();
        $result = $sanitizer->statement('Analise as afirmativas. Está correto o que se afirma em a) I, apenas. b) II, apenas. c) III, apenas.', [['content' => 'I'], ['content' => 'II'], ['content' => 'III']]);
        self::assertSame('Analise as afirmativas. Está correto o que se afirma em', $result);
    }
}