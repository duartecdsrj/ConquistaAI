<?php
declare(strict_types=1);
namespace App\Application\Study\DTO\Request;
final readonly class UpdateStudyGoalRequestDto { public function __construct(public int $weeklyQuestionGoal) {} }
