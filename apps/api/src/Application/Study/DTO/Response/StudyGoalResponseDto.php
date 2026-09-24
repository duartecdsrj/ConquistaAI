<?php
declare(strict_types=1);
namespace App\Application\Study\DTO\Response;
final readonly class StudyGoalResponseDto { public function __construct(public int $weeklyQuestionGoal,public int $completedQuestions,public float $percentage,public string $periodStartsAt) {} }
