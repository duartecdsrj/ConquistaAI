<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Provider;
use App\Application\QuestionLearning\Port\QuestionExplanationProviderInterface;
final readonly class QuestionExplanationProviderFactory { /** @param array<string,QuestionExplanationProviderInterface> $providers */ public function __construct(private array $providers) {} public function create(string $name): QuestionExplanationProviderInterface { return $this->providers[$name] ?? new UnavailableQuestionExplanationProvider($name); } }
