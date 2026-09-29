<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\ValueObject;
final readonly class QuestionExplanationJob { public function __construct(public string $executionId, public string $userId, public string $questionId, public ?string $attemptId, public string $algorithmVersion, public string $safetyMode) {} }
