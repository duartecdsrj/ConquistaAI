<?php
declare(strict_types=1);
namespace Tests\Unit\QuestionBank;
use App\Application\QuestionBank\AI\QuestionImportAnalysisResponseValidator;
use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Request\QuestionImportEvidencePageDto;
use PHPUnit\Framework\TestCase;
final class QuestionImportAnalysisResponseValidatorTest extends TestCase
{
    public function testAcceptsEvidenceBoundConflictWithoutRevealingAnswer(): void
    {
        $result=(new QuestionImportAnalysisResponseValidator())->validate($this->payload('Revisão editorial recomendada.'),$this->candidate());
        self::assertSame('CONFLICT',$result->answerKeyAssessment->value);
        self::assertSame('ANSWER_KEY_CONFLICT',$result->findings[0]->code->value);
    }
    public function testRejectsFindingThatRevealsAnOption(): void
    {
        $this->expectException(\DomainException::class);
        (new QuestionImportAnalysisResponseValidator())->validate($this->payload('O gabarito A está incorreto.'),$this->candidate());
    }
    public function testAcceptsEmptyMetadataEvidenceWhenMetadataIsAbsent(): void
    {
        $result=(new QuestionImportAnalysisResponseValidator())->validate($this->payload('Revisão editorial recomendada.'),$this->candidate());
        self::assertNull($result->metadata->exam);
        self::assertSame([], $result->metadata->evidencePages['exam']);
    }
    public function testNormalizesAnAllowedPathReturnedInOneArrayElement(): void
    {
        $payload=$this->payload('Revisão editorial recomendada.');
        $payload['taxonomy_path']=['Direito > Constitucional'];
        $result=(new QuestionImportAnalysisResponseValidator())->validate($payload,$this->candidate());
        self::assertSame(['Direito','Constitucional'],$result->taxonomyPath);
    }
    public function testRejectsTaxonomyOutsideThePermittedLeaves(): void
    {
        $payload=$this->payload('Revisão editorial recomendada.');
        $payload['taxonomy_path']=['Direito'];
        $this->expectException(\DomainException::class);
        (new QuestionImportAnalysisResponseValidator())->validate($payload,$this->candidate());
    }
    private function candidate(): AnalyzeQuestionImportCandidateRequestDto { return new AnalyzeQuestionImportCandidateRequestDto(str_repeat('a',64),[new QuestionImportEvidencePageDto(1,'Questão de prova.')],[],['Direito > Constitucional']); }
    private function payload(string $summary): array { return ['schema_version'=>'question-import-analysis-v1','statement'=>'Enunciado da questão.','options'=>[['label'=>'A','content'=>'Opção A'],['label'=>'B','content'=>'Opção B'],['label'=>'C','content'=>'Opção C'],['label'=>'D','content'=>'Opção D']],'structure_type'=>'MULTIPLE_CHOICE','metadata'=>['exam'=>null,'position'=>null,'board'=>null,'year'=>null,'evidence_pages'=>[]],'evidence_pages'=>[1],'taxonomy_path'=>['Direito','Constitucional'],'parent_subject'=>null,'difficulty'=>'MEDIUM','correct_option'=>'A','answer_key_assessment'=>'CONFLICT','image_assessment'=>'NOT_APPLICABLE','findings'=>[['code'=>'ANSWER_KEY_CONFLICT','severity'=>'WARNING','confidence'=>.8,'safe_summary'=>$summary,'evidence_pages'=>[1]]],'image_anchors'=>[]]; }
}
