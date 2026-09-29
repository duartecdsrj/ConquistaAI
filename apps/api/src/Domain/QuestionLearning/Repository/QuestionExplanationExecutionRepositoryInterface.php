<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Repository;
use App\Domain\QuestionLearning\Entity\QuestionExplanationExecution;
use App\Domain\QuestionLearning\ValueObject\QuestionExplanationJob;
interface QuestionExplanationExecutionRepositoryInterface { public function findById(string $id): ?QuestionExplanationExecution; public function findByRequest(string $userId, string $questionId, ?string $attemptId, string $algorithmVersion): ?QuestionExplanationExecution; public function findLatestForUserQuestion(string $userId, string $questionId): ?QuestionExplanationExecution; public function claimNextPending(\DateTimeImmutable $now): ?QuestionExplanationJob; public function save(QuestionExplanationExecution $execution): void; }
