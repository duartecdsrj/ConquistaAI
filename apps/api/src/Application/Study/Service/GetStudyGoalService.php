<?php
declare(strict_types=1);
namespace App\Application\Study\Service;
use App\Application\Study\DTO\Response\StudyGoalResponseDto;
use App\Domain\Study\Repository\StudyGoalRepositoryInterface;
final class GetStudyGoalService { public function __construct(private readonly StudyGoalRepositoryInterface $goals,private readonly \DateTimeZone $utc=new \DateTimeZone('UTC')){} public function getForUser(string $userId): StudyGoalResponseDto { $goal=$this->goals->findForUser($userId);$target=$goal?->weeklyQuestionGoal??20;$start=(new \DateTimeImmutable('monday this week 00:00:00',$this->utc));$completed=$this->goals->completedAnswersSince($userId,$start);return new StudyGoalResponseDto($target,$completed,min(100.0,round(($completed/$target)*100,2)),$start->format(DATE_ATOM)); } }
