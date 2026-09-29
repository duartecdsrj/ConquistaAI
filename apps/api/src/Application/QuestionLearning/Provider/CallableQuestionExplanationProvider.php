<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Provider;
use App\Application\QuestionLearning\DTO\Response\QuestionExplanationProviderResponseDto;use App\Application\QuestionLearning\Port\QuestionExplanationProviderInterface;use App\Domain\QuestionLearning\ValueObject\QuestionExplanationContext;
final class CallableQuestionExplanationProvider implements QuestionExplanationProviderInterface { private \Closure $client; /** @param callable(QuestionExplanationContext,array):QuestionExplanationProviderResponseDto $client */ public function __construct(callable $client) { $this->client=\Closure::fromCallable($client); } public function explain(QuestionExplanationContext $context,array $schema):QuestionExplanationProviderResponseDto { return ($this->client)($context,$schema); } }
