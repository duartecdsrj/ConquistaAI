<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Entity;
use App\Domain\QuestionLearning\Enum\LearningEventType;
use App\Domain\QuestionLearning\Enum\LearningOutcome;
final readonly class LearningEvent { /** @param array<string, scalar|null> $payload */ public function __construct(public string $id, public string $userId, public string $questionId, public ?string $attemptId, public ?string $taxonomySubjectId, public LearningEventType $type, public ?LearningOutcome $outcome, public ?int $elapsedSeconds, public ?string $origin, public array $payload, public \DateTimeImmutable $occurredAt) {} }
