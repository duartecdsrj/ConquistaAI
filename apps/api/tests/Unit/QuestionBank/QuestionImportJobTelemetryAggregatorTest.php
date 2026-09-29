<?php
declare(strict_types=1);
namespace Tests\Unit\QuestionBank;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalysisFindingDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalysisOutputDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzedOptionDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzerResponseDto;
use App\Application\QuestionBank\Service\QuestionImportJobTelemetryAggregator;
use App\Domain\QuestionBank\Entity\QuestionPdfImportJob;
use App\Domain\QuestionBank\Enum\QuestionImportAnswerKeyAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportFindingCode;
use App\Domain\QuestionBank\Enum\QuestionImportFindingSeverity;
use App\Domain\QuestionBank\Enum\QuestionImportImageAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportStructureType;
use App\Domain\QuestionBank\Enum\QuestionImportUsageAvailability;
use App\Domain\QuestionBank\ValueObject\QuestionImportMetadata;
use App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage;
use PHPUnit\Framework\TestCase;
final class QuestionImportJobTelemetryAggregatorTest extends TestCase
{
    public function testAggregatesReportedUsageAndFindings(): void
    {
        $job = new QuestionPdfImportJob('job',null,'user','/tmp/a.pdf',str_repeat('a',64),'a.pdf','PROCESSING',0,0,0,0,0,null,0,0,0,0,0,0,null,new \DateTimeImmutable());
        $first = QuestionImportJobTelemetryAggregator::append($job,$this->response(new QuestionImportTokenUsage(QuestionImportUsageAvailability::AVAILABLE,10,5,15,'0.010000'),100));
        $second = QuestionImportJobTelemetryAggregator::append($first,$this->response(new QuestionImportTokenUsage(QuestionImportUsageAvailability::AVAILABLE,20,8,28,'0.020000'),200));
        self::assertSame(2,$second->analysisTelemetry?->analysisCount);
        self::assertSame(300,$second->analysisTelemetry?->durationMilliseconds);
        self::assertSame(30,$second->analysisTelemetry?->tokenUsage->inputTokens);
        self::assertSame(13,$second->analysisTelemetry?->tokenUsage->outputTokens);
        self::assertSame(43,$second->analysisTelemetry?->tokenUsage->totalTokens);
        self::assertSame('0.030000',$second->analysisTelemetry?->tokenUsage->costUsd);
        self::assertSame(2,$second->analysisTelemetry?->answerKeyConflictCount);
        self::assertSame(2,$second->analysisTelemetry?->imageDiscrepancyCount);
        self::assertSame(2,$second->analysisTelemetry?->structuralIssueCount);
    }
    public function testMarksUsageUnavailableWhenProviderDoesNotReportIt(): void
    {
        $job = new QuestionPdfImportJob('job',null,'user','/tmp/a.pdf',str_repeat('a',64),'a.pdf','PROCESSING',0,0,0,0,0,null,0,0,0,0,0,0,null,new \DateTimeImmutable());
        $result = QuestionImportJobTelemetryAggregator::append($job,$this->response(QuestionImportTokenUsage::unavailable(),100));
        self::assertSame(QuestionImportUsageAvailability::UNAVAILABLE,$result->analysisTelemetry?->tokenUsage->availability);
        self::assertNull($result->analysisTelemetry?->tokenUsage->totalTokens);
    }
    private function response(QuestionImportTokenUsage $usage,int $duration): QuestionImportAnalyzerResponseDto
    {
        $options=[]; foreach (['A','B','C','D','E'] as $label) $options[]=new QuestionImportAnalyzedOptionDto($label,'Opção '.$label);
        $findings=[]; foreach ([QuestionImportFindingCode::ANSWER_KEY_CONFLICT,QuestionImportFindingCode::IMAGE_DISCREPANCY,QuestionImportFindingCode::STRUCTURAL_INCONSISTENCY] as $code) $findings[]=new QuestionImportAnalysisFindingDto($code,QuestionImportFindingSeverity::WARNING,.8,'Revisão editorial recomendada.',[1]);
        return new QuestionImportAnalyzerResponseDto(new QuestionImportAnalysisOutputDto('question-import-analysis-v1','Enunciado',$options,QuestionImportStructureType::MULTIPLE_CHOICE,new QuestionImportMetadata(null,null,null,null,[]),[1],[],null,'MEDIUM','A',QuestionImportAnswerKeyAssessment::CONFLICT,QuestionImportImageAssessment::DISCREPANCY,$findings,[]),'codex','gpt-5',$usage,$duration);
    }
}
