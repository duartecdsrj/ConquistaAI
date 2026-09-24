<?php
declare(strict_types=1);
namespace App\Application\Study\Service;
use App\Application\Study\DTO\Request\UpdateStudyGoalRequestDto;
use App\Application\Study\DTO\Response\StudyGoalResponseDto;
use App\Domain\Study\Entity\StudyGoal;
use App\Domain\Study\Repository\StudyGoalRepositoryInterface;
final class UpdateStudyGoalService { public function __construct(private readonly StudyGoalRepositoryInterface $goals,private readonly GetStudyGoalService $reader,private readonly \DateTimeZone $utc=new \DateTimeZone('UTC')){} public function updateForUser(string $userId,UpdateStudyGoalRequestDto $request): StudyGoalResponseDto { $this->goals->save(new StudyGoal($userId,$request->weeklyQuestionGoal,new \DateTimeImmutable('now',$this->utc)));return $this->reader->getForUser($userId); } }
