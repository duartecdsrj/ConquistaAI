<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionBank;

use App\Domain\QuestionBank\Service\QuestionAuditAnalyzer;
use PHPUnit\Framework\TestCase;

final class QuestionAuditAnalyzerTest extends TestCase
{
    public function testDetectsMissingVisualAndKeepsItForReview(): void
    {
        $findings = (new QuestionAuditAnalyzer())->inspect('Observe a figura abaixo.', ['a', 'b', 'c', 'd']);
        self::assertSame('IMAGEM_NAO_LOCALIZADA', $findings[0]['code']);
        self::assertSame('MEDIUM', $findings[0]['confidence']);
    }

    public function testDoesNotRepeatIncompleteExtractionFinding(): void
    {
        $findings = (new QuestionAuditAnalyzer())->inspect('', ['', 'a', 'a']);
        self::assertCount(1, array_filter($findings, static fn (array $finding): bool => $finding['code'] === 'EXTRACAO_INCOMPLETA'));
    }
    public function testDistinguishesCodeAndCorrelationPresentationFindings(): void
    {
        $findings = (new QuestionAuditAnalyzer())->inspect("Correlacione as colunas:\nI. Primeiro item\nII. Segundo item\n( ) Afirmação um\n( ) Afirmação dois\n\nSELECT * FROM usuarios;", ['a', 'b', 'c', 'd']);
        $codes = array_column($findings, 'code');

        self::assertContains('CODIGO_SEM_BLOCO', $codes);
        self::assertContains('ESTRUTURA_CORRELACAO', $codes);
    }

    public function testDetectsShellCommandWithoutFence(): void
    {
        $findings = (new QuestionAuditAnalyzer())->inspect("Execute:\ncurl -fsS https://example.test", ['a', 'b', 'c', 'd']);

        self::assertContains('CODIGO_SEM_BLOCO', array_column($findings, 'code'));
    }

    public function testDetectsReplacementCharacterAsCorruptedExtraction(): void
    {
        $findings = (new QuestionAuditAnalyzer())->inspect('Enunciado com caractere � extraído.', ['a', 'b', 'c', 'd']);

        self::assertContains('CARACTERE_CORROMPIDO', array_column($findings, 'code'));
    }

    public function testDetectsDocumentBodyLeakWithoutRemovingIt(): void
    {
        $findings = (new QuestionAuditAnalyzer())->inspect("Enunciado válido.\n\nGABARITO\n1. C", ['a', 'b', 'c', 'd']);

        self::assertContains('CONTEUDO_DOCUMENTAL_MESCLADO', array_column($findings, 'code'));
    }

}
