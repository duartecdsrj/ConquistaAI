<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Port;
use App\Application\QuestionLearning\DTO\Response\QuestionExplanationProviderResponseDto;use App\Domain\QuestionLearning\ValueObject\QuestionExplanationContext;
interface QuestionExplanationProviderInterface { public function explain(QuestionExplanationContext $context, array $schema): QuestionExplanationProviderResponseDto; }
