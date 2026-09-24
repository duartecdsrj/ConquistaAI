<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Study;
use App\Domain\Study\Entity\StudyGoal;
use App\Domain\Study\Repository\StudyGoalRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Performance\Entity\AnswerRecord;
use App\Infrastructure\Persistence\Doctrine\Performance\Entity\AttemptRecord;
use App\Infrastructure\Persistence\Doctrine\Study\Entity\StudyGoalRecord;
use Doctrine\ORM\EntityManagerInterface;
final class DoctrineStudyGoalRepository implements StudyGoalRepositoryInterface {
 public function __construct(private readonly EntityManagerInterface $entityManager) {}
 public function findForUser(string $userId): ?StudyGoal { $record=$this->entityManager->find(StudyGoalRecord::class,$userId);return $record instanceof StudyGoalRecord?new StudyGoal($record->userId,$record->weeklyQuestionGoal,$record->updatedAt):null; }
 public function save(StudyGoal $goal): void { $record=$this->entityManager->find(StudyGoalRecord::class,$goal->userId);if(!$record instanceof StudyGoalRecord){$record=new StudyGoalRecord();$record->userId=$goal->userId;$this->entityManager->persist($record);}$record->weeklyQuestionGoal=$goal->weeklyQuestionGoal;$record->updatedAt=$goal->updatedAt; }
 public function completedAnswersSince(string $userId, \DateTimeImmutable $since): int { return (int)$this->entityManager->createQueryBuilder()->select('COUNT(answer.id)')->from(AnswerRecord::class,'answer')->innerJoin(AttemptRecord::class,'attempt','WITH','attempt.id = answer.attemptId')->where('attempt.userId = :userId')->andWhere('attempt.completedAt >= :since')->andWhere('attempt.finalAnswerId = answer.id')->setParameter('userId',$userId)->setParameter('since',$since)->getQuery()->getSingleScalarResult(); }
}
