<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\ValueObject;
final readonly class CompletedAttemptLearningSignal { /** @param list<string> $taxonomySubjectIds */ public function __construct(public string $questionId, public string $selectedOptionId, public bool $correct, public int $elapsedSeconds, public string $origin, public array $taxonomySubjectIds) {} }
