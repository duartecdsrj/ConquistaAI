<?php
declare(strict_types=1);

namespace App\Domain\Performance\Repository;

use App\Domain\Performance\Entity\Answer;
use App\Domain\Performance\Entity\Attempt;

interface AttemptRepositoryInterface
{
    public function saveAttempt(Attempt $attempt): void;

    /** Appends a new answer; prior answers are never updated. */
    public function appendAnswer(Answer $answer): void;

    /** @return list<Answer> */
    public function listAnswers(string $attemptId): array;
}
