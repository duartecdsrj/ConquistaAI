<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Service;
use App\Domain\QuestionLearning\Enum\ExplanationSafetyMode;
final readonly class QuestionExplanationSafetyPolicy { public function mode(?bool $attemptCompleted): ExplanationSafetyMode { return $attemptCompleted === true ? ExplanationSafetyMode::POST_ANSWER : ExplanationSafetyMode::CONCEPTUAL_ONLY; } public function allowsAnswerKey(ExplanationSafetyMode $mode): bool { return $mode === ExplanationSafetyMode::POST_ANSWER; } }
