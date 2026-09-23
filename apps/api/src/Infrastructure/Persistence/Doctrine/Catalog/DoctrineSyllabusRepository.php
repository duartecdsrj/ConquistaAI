<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Catalog;
use App\Domain\Catalog\Entity\Syllabus; use App\Domain\Catalog\Repository\SyllabusRepositoryInterface; use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\SyllabusRecord; use Doctrine\ORM\EntityManagerInterface;
final class DoctrineSyllabusRepository implements SyllabusRepositoryInterface {
 public function __construct(private readonly EntityManagerInterface $entityManager) {}
 public function save(Syllabus $syllabus): void { $r=new SyllabusRecord(); $r->id=$syllabus->id; $r->positionId=$syllabus->positionId; $r->name=$syllabus->name; $r->publishedAt=$syllabus->publishedAt===null?null:new \DateTimeImmutable($syllabus->publishedAt); $r->sourceUrl=$syllabus->sourceUrl; $r->createdAt=new \DateTimeImmutable('now'); $r->updatedAt=$r->createdAt; $this->entityManager->persist($r); }
 public function existsById(string $id): bool { return (bool)$this->entityManager->createQueryBuilder()->select('COUNT(syllabus.id)')->from(SyllabusRecord::class,'syllabus')->where('syllabus.id=:id')->setParameter('id',$id)->getQuery()->getSingleScalarResult(); }
 public function existsForPosition(string $id,string $positionId): bool { return (bool)$this->entityManager->createQueryBuilder()->select('COUNT(syllabus.id)')->from(SyllabusRecord::class,'syllabus')->where('syllabus.id=:id')->andWhere('syllabus.positionId=:positionId')->setParameter('id',$id)->setParameter('positionId',$positionId)->getQuery()->getSingleScalarResult(); }
 public function listForPosition(string $positionId): array { return array_map(static fn(SyllabusRecord $r): Syllabus=>new Syllabus($r->id,$r->positionId,$r->name,$r->publishedAt?->format('Y-m-d'),$r->sourceUrl),$this->entityManager->createQueryBuilder()->select('syllabus')->from(SyllabusRecord::class,'syllabus')->where('syllabus.positionId=:positionId')->setParameter('positionId',$positionId)->orderBy('syllabus.name','ASC')->getQuery()->getResult()); }
}
