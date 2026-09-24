<?php
declare(strict_types=1);
namespace App\Domain\Study\Entity;
final readonly class StudyGoal { public function __construct(public string $userId, public int $weeklyQuestionGoal, public \DateTimeImmutable $updatedAt) { if($weeklyQuestionGoal<1||$weeklyQuestionGoal>500) throw new \InvalidArgumentException('A meta semanal deve estar entre 1 e 500 questões.'); } }
