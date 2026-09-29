<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Repository;
use App\Domain\QuestionLearning\ValueObject\CompletedAttemptLearningSignal;
interface CompletedAttemptLearningSignalReaderInterface { public function read(string $questionId, string $selectedOptionId, int $elapsedSeconds, string $origin): ?CompletedAttemptLearningSignal; /** @return list<string> */ public function taxonomySubjectIdsForQuestion(string $questionId): array; }
