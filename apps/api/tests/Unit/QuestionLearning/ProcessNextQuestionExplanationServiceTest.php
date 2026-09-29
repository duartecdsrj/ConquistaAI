<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionLearning;

use App\Application\QuestionLearning\AI\QuestionExplanationResponseValidator;
use App\Application\QuestionLearning\DTO\Response\QuestionExplanationProviderResponseDto;
use App\Application\QuestionLearning\Port\QuestionExplanationContextReaderInterface;
use App\Application\QuestionLearning\Port\QuestionExplanationProviderInterface;
use App\Application\QuestionLearning\Provider\QuestionExplanationProviderFactory;
use App\Application\QuestionLearning\Service\ProcessNextQuestionExplanationService;
use App\Domain\QuestionLearning\Entity\QuestionExplanationExecution;
use App\Domain\QuestionLearning\Enum\ExplanationSafetyMode;
use App\Domain\QuestionLearning\Enum\ExplanationStatus;
use App\Domain\QuestionLearning\Repository\QuestionExplanationExecutionRepositoryInterface;
use App\Domain\QuestionLearning\ValueObject\QuestionExplanationContext;
use App\Domain\QuestionLearning\ValueObject\QuestionExplanationJob;
use PHPUnit\Framework\TestCase;

final class ProcessNextQuestionExplanationServiceTest extends TestCase
{
    public function testCompletesClaimedExecutionWithFakeProvider(): void
    {
        [$repository, $execution] = $this->repository();
        $provider = new class implements QuestionExplanationProviderInterface { public function explain(QuestionExplanationContext $context,array $schema):QuestionExplanationProviderResponseDto{return new QuestionExplanationProviderResponseDto(['summary'=>'Revise a regra aplicada.','concepts'=>['regra'],'reinforcement'=>'Pratique um exemplo semelhante.'],'fake','test-model',12,34);} };
        $service = new ProcessNextQuestionExplanationService($repository, $this->context(), $provider, new QuestionExplanationResponseValidator());
        self::assertTrue($service->execute(new \DateTimeImmutable('2026-01-01T00:00:00Z')));
        self::assertSame(ExplanationStatus::COMPLETED, $execution->status());
        self::assertSame('fake', $execution->provider());
        self::assertSame(12, $execution->tokenCount());
    }

    public function testFailureIsSanitizedAndCanBeRetried(): void
    {
        [$repository, $execution] = $this->repository();
        $provider = new class implements QuestionExplanationProviderInterface { public function explain(QuestionExplanationContext $context,array $schema):QuestionExplanationProviderResponseDto{throw new \RuntimeException('secret provider detail');} };
        $service = new ProcessNextQuestionExplanationService($repository, $this->context(), $provider, new QuestionExplanationResponseValidator());
        self::assertFalse($service->execute(new \DateTimeImmutable('2026-01-01T00:00:00Z')));
        self::assertSame(ExplanationStatus::FAILED, $execution->status());
        self::assertSame(1, $execution->retryCount());
        self::assertSame('Não foi possível processar a explicação da questão.', $execution->errorMessage());
    }

    public function testFactoryUsesConfiguredProviderAndSafeFallback(): void
    {
        $provider = new class implements QuestionExplanationProviderInterface { public function explain(QuestionExplanationContext $context,array $schema):QuestionExplanationProviderResponseDto{throw new \LogicException();} };
        $factory = new QuestionExplanationProviderFactory(['fake' => $provider]);
        self::assertSame($provider, $factory->create('fake'));
        $this->expectException(\RuntimeException::class);
        $factory->create('unknown')->explain(new QuestionExplanationContext('question-1','',[],[],'CONCEPTUAL_ONLY'), []);
    }

    /** @return array{0:QuestionExplanationExecutionRepositoryInterface,1:QuestionExplanationExecution} */
    private function repository(): array
    {
        $execution = new QuestionExplanationExecution('execution-1','user-1','question-1',null,'v1',ExplanationSafetyMode::CONCEPTUAL_ONLY,ExplanationStatus::PENDING,0,null,null,null,null,null,null,null,new \DateTimeImmutable('2026-01-01T00:00:00Z'));
        $repository = new class($execution) implements QuestionExplanationExecutionRepositoryInterface { public function __construct(private QuestionExplanationExecution $execution) {} public function findById(string $id):?QuestionExplanationExecution{return $id===$this->execution->id?$this->execution:null;} public function findByRequest(string $userId,string $questionId,?string $attemptId,string $algorithmVersion):?QuestionExplanationExecution{return null;} public function findLatestForUserQuestion(string $userId,string $questionId):?QuestionExplanationExecution{return null;} public function claimNextPending(\DateTimeImmutable $now):?QuestionExplanationJob{if($this->execution->status()!==ExplanationStatus::PENDING)return null;$this->execution->begin($now);return new QuestionExplanationJob($this->execution->id,$this->execution->userId,$this->execution->questionId,$this->execution->attemptId,$this->execution->algorithmVersion,$this->execution->safetyMode->value);} public function save(QuestionExplanationExecution $execution):void{} };
        return [$repository, $execution];
    }

    private function context(): QuestionExplanationContextReaderInterface
    {
        return new class implements QuestionExplanationContextReaderInterface { public function read(QuestionExplanationExecution $execution):?QuestionExplanationContext{return new QuestionExplanationContext($execution->questionId,'Enunciado',[],[],'CONCEPTUAL_ONLY');} };
    }
}
