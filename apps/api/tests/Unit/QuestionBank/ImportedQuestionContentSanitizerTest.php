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
    public function testRemovesPdfFooterAndDecodesSerializedLineBreaks(): void
    {
        $sanitizer = new ImportedQuestionContentSanitizer();
        $result = $sanitizer->option("compressão\\n\\n Concursos da Área Fiscal Especialidade TI - Arquitetura e Sistemas Operacionais 183\\n\\nEvandro Dalla Vecchia, Equipe Informática e TI\\n\\n");
        self::assertSame('compressão', $result);
    }
    public function testCutsCourseCommentaryAndStandalonePageNumber(): void
    {
        $sanitizer = new ImportedQuestionContentSanitizer();
        self::assertSame('Enunciado válido.', $sanitizer->statement("Enunciado válido.\n\n4\n\nComentários:\nExplicação do curso", []));
    }
    public function testPreservesWindowsPathThatStartsWithBackslashN(): void
    {
        $sanitizer = new ImportedQuestionContentSanitizer();
        self::assertSame('\\net\\web', $sanitizer->option('\\net\\web'));
        self::assertSame("Primeiro\nSegundo", $sanitizer->option('Primeiro\\nSegundo'));
    }
}