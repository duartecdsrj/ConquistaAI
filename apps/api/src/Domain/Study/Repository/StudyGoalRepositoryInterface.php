<?php
declare(strict_types=1);
namespace App\Domain\Study\Repository;
use App\Domain\Study\Entity\StudyGoal;
interface StudyGoalRepositoryInterface { public function findForUser(string $userId): ?StudyGoal; public function save(StudyGoal $goal): void; public function completedAnswersSince(string $userId, \DateTimeImmutable $since): int; }
