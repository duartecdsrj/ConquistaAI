<?php
declare(strict_types=1);
namespace Tests\Unit\QuestionBank;
use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Request\QuestionImportEvidencePageDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzerResponseDto;
use App\Application\QuestionBank\Port\QuestionImportAnalyzerInterface;
use App\Application\QuestionBank\Provider\QuestionImportAnalyzerFactory;
use PHPUnit\Framework\TestCase;
final class QuestionImportAnalyzerFactoryTest extends TestCase
{
    public function testResolvesConfiguredProviderCaseInsensitively(): void
    {
        $provider=new class implements QuestionImportAnalyzerInterface { public function analyze(AnalyzeQuestionImportCandidateRequestDto $candidate): QuestionImportAnalyzerResponseDto { throw new \LogicException(); } };
        self::assertSame($provider,(new QuestionImportAnalyzerFactory(['codex'=>$provider]))->create(' CoDeX '));
    }
    public function testUnknownProviderFailsExplicitlyWithoutFallback(): void
    {
        $candidate=new AnalyzeQuestionImportCandidateRequestDto(str_repeat('a',64),[new QuestionImportEvidencePageDto(1,'Conteúdo')],[],['Direito']);
        $this->expectException(\DomainException::class);
        (new QuestionImportAnalyzerFactory([]))->create('future-api')->analyze($candidate);
    }
}
