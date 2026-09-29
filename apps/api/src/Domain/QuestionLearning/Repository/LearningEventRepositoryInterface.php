<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Repository;
use App\Domain\QuestionLearning\Entity\LearningEvent;
interface LearningEventRepositoryInterface { public function append(LearningEvent $event): void; /** @return list<LearningEvent> */ public function listForUserSubject(string $userId, string $taxonomySubjectId, \DateTimeImmutable $since): array; }
