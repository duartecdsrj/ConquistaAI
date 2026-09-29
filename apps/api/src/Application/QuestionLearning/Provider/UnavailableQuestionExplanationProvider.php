<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Provider;
use App\Application\QuestionLearning\DTO\Response\QuestionExplanationProviderResponseDto;use App\Application\QuestionLearning\Port\QuestionExplanationProviderInterface;use App\Domain\QuestionLearning\ValueObject\QuestionExplanationContext;
final readonly class UnavailableQuestionExplanationProvider implements QuestionExplanationProviderInterface { public function __construct(private string $name = 'unconfigured') {} public function explain(QuestionExplanationContext $context,array $schema):QuestionExplanationProviderResponseDto { throw new \RuntimeException('Provider de explicação indisponível: '.$this->name); } }
