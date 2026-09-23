<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Catalog\Entity;
use DateTimeImmutable;use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]#[ORM\Table(name:'subjects')]
class SubjectRecord {#[ORM\Id]#[ORM\Column(type:'string',length:36)]public string $id;#[ORM\Column(name:'syllabus_id',type:'string',length:36)]public string $syllabusId;#[ORM\Column(name:'parent_id',type:'string',length:36,nullable:true)]public ?string $parentId;#[ORM\Column(type:'string',length:190)]public string $name;#[ORM\Column(name:'sort_order',type:'integer')]public int $sortOrder;#[ORM\Column(name:'created_at',type:'datetime_immutable')]public DateTimeImmutable $createdAt;#[ORM\Column(name:'updated_at',type:'datetime_immutable')]public DateTimeImmutable $updatedAt;}
