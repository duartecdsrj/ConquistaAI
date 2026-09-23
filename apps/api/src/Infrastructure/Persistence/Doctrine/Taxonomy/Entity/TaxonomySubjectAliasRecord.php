<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Taxonomy\Entity;
use DateTimeImmutable;use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name:'taxonomy_subject_aliases')]
class TaxonomySubjectAliasRecord { #[ORM\Id] #[ORM\Column(type:'string',length:36)] public string $id; #[ORM\Column(name:'subject_id',type:'string',length:36)] public string $subjectId; #[ORM\Column(type:'string',length:190)] public string $alias; #[ORM\Column(name:'normalized_alias',type:'string',length:190)] public string $normalizedAlias; #[ORM\Column(name:'created_at',type:'datetime_immutable')] public DateTimeImmutable $createdAt; }
