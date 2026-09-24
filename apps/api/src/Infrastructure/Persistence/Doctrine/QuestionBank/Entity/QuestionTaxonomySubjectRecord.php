<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;
use DateTimeImmutable;use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name:'question_taxonomy_subjects')]
class QuestionTaxonomySubjectRecord { #[ORM\Id] #[ORM\Column(name:'question_id',type:'string',length:36)] public string $questionId; #[ORM\Id] #[ORM\Column(name:'taxonomy_subject_id',type:'string',length:36)] public string $taxonomySubjectId; #[ORM\Column(name:'created_at',type:'datetime_immutable')] public DateTimeImmutable $createdAt; }
