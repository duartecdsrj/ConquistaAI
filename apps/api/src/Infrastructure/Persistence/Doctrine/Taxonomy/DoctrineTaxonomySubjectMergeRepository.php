<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Taxonomy;
use App\Domain\Taxonomy\Entity\TaxonomySubjectMerge;use App\Domain\Taxonomy\Repository\TaxonomySubjectMergeRepositoryInterface;use App\Infrastructure\Persistence\Doctrine\Taxonomy\Entity\TaxonomySubjectMergeRecord;use Doctrine\ORM\EntityManagerInterface;
final class DoctrineTaxonomySubjectMergeRepository implements TaxonomySubjectMergeRepositoryInterface { public function __construct(private readonly EntityManagerInterface $entityManager){} public function save(TaxonomySubjectMerge $merge):void{$record=new TaxonomySubjectMergeRecord();$record->id=$merge->id;$record->sourceSubjectId=$merge->sourceSubjectId;$record->targetSubjectId=$merge->targetSubjectId;$record->mergedBy=$merge->mergedBy;$record->reason=$merge->reason;$record->createdAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));$this->entityManager->persist($record);} }
