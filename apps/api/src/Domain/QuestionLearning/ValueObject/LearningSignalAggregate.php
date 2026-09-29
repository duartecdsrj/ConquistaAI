<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\ValueObject;
final readonly class LearningSignalAggregate { public function __construct(public string $userId, public string $taxonomySubjectId, public int $answerCount, public int $correctCount, public int $incorrectCount, public int $notMasteredActivations, public int $notMasteredDeactivations, public ?\DateTimeImmutable $lastOccurredAt) {} }
