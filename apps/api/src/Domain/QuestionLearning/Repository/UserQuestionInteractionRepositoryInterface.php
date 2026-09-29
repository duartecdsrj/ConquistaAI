<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Repository;
use App\Domain\QuestionLearning\Entity\UserQuestionInteraction;
interface UserQuestionInteractionRepositoryInterface { public function find(string $userId, string $questionId): ?UserQuestionInteraction; public function save(UserQuestionInteraction $interaction): void; }
