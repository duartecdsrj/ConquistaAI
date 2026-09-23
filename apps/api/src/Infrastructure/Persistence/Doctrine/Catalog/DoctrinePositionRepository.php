<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Catalog;
use App\Domain\Catalog\Entity\Position; use App\Domain\Catalog\Repository\PositionRepositoryInterface; use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\PositionRecord; use Doctrine\ORM\EntityManagerInterface;
final class DoctrinePositionRepository implements PositionRepositoryInterface {
 public function __construct(private readonly EntityManagerInterface $entityManager) {}
 public function save(Position $position): void { $r=new PositionRecord(); $r->id=$position->id; $r->examId=$position->examId; $r->name=$position->name; $r->emphasis=$position->emphasis; $r->createdAt=new \DateTimeImmutable('now'); $r->updatedAt=$r->createdAt; $this->entityManager->persist($r); }
 public function existsForExam(string $id,string $examId): bool { return (bool)$this->entityManager->createQueryBuilder()->select('COUNT(position.id)')->from(PositionRecord::class,'position')->where('position.id=:id')->andWhere('position.examId=:examId')->setParameter('id',$id)->setParameter('examId',$examId)->getQuery()->getSingleScalarResult(); }
 public function existsById(string $id): bool { return (bool)$this->entityManager->createQueryBuilder()->select('COUNT(position.id)')->from(PositionRecord::class,'position')->where('position.id=:id')->setParameter('id',$id)->getQuery()->getSingleScalarResult(); }
 public function listForExam(string $examId): array { return array_map(static fn(PositionRecord $r): Position=>new Position($r->id,$r->examId,$r->name,$r->emphasis),$this->entityManager->createQueryBuilder()->select('position')->from(PositionRecord::class,'position')->where('position.examId=:examId')->setParameter('examId',$examId)->orderBy('position.name','ASC')->getQuery()->getResult()); }
}
