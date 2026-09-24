<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Taxonomy\Entity;
use DateTimeImmutable;use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name:'taxonomy_subject_merges')]
class TaxonomySubjectMergeRecord { #[ORM\Id] #[ORM\Column(type:'string',length:36)] public string $id; #[ORM\Column(name:'source_subject_id',type:'string',length:36)] public string $sourceSubjectId; #[ORM\Column(name:'target_subject_id',type:'string',length:36)] public string $targetSubjectId; #[ORM\Column(name:'merged_by',type:'string',length:36)] public string $mergedBy; #[ORM\Column(type:'string',length:1000)] public string $reason; #[ORM\Column(name:'created_at',type:'datetime_immutable')] public DateTimeImmutable $createdAt; }
