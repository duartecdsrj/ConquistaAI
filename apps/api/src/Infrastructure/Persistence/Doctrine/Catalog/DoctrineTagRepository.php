<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Catalog;
use App\Domain\Catalog\Entity\Tag;
use App\Domain\Catalog\Repository\TagRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\TagRecord;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
final class DoctrineTagRepository implements TagRepositoryInterface {
 public function __construct(private readonly EntityManagerInterface $entityManager) {}
 public function save(Tag $tag): void { $record=new TagRecord(); $record->id=$tag->id; $record->name=$tag->name; $record->createdAt=new DateTimeImmutable('now'); $this->entityManager->persist($record); }
 public function existsByName(string $name): bool { return (bool) $this->entityManager->createQueryBuilder()->select('COUNT(tag.id)')->from(TagRecord::class,'tag')->where('tag.name = :name')->setParameter('name',$name)->getQuery()->getSingleScalarResult(); }
 public function list(): array { return array_map(static fn(TagRecord $tag):Tag=>new Tag($tag->id,$tag->name),$this->entityManager->createQueryBuilder()->select('tag')->from(TagRecord::class,'tag')->orderBy('tag.name','ASC')->getQuery()->getResult()); }
}
