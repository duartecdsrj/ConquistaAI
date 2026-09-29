<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Entity;
use App\Domain\QuestionLearning\ValueObject\QuestionInteractionState;
final class UserQuestionInteraction { public function __construct(public readonly string $userId, public readonly string $questionId, private QuestionInteractionState $state, public readonly \DateTimeImmutable $createdAt, private \DateTimeImmutable $updatedAt) {} public function state(): QuestionInteractionState { return $this->state; } public function update(QuestionInteractionState $state, \DateTimeImmutable $now): bool { $changed = $this->state != $state; $this->state = $state; if ($changed) { $this->updatedAt = $now; } return $changed; } public function updatedAt(): \DateTimeImmutable { return $this->updatedAt; } }
