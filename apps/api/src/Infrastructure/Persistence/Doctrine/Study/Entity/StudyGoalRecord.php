<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Study\Entity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name:'study_goals')]
class StudyGoalRecord { #[ORM\Id] #[ORM\Column(name:'user_id',type:'string',length:36)] public string $userId; #[ORM\Column(name:'weekly_question_goal',type:'smallint')] public int $weeklyQuestionGoal; #[ORM\Column(name:'updated_at',type:'datetime_immutable')] public DateTimeImmutable $updatedAt; }
