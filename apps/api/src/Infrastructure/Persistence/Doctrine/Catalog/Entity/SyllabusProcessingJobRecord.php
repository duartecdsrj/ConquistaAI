<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Catalog\Entity;
use DateTimeImmutable;use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name:'syllabus_processing_jobs')]
class SyllabusProcessingJobRecord { #[ORM\Id] #[ORM\Column(type:'string',length:36)] public string $id; #[ORM\Column(name:'syllabus_id',type:'string',length:36)] public string $syllabusId; #[ORM\Column(name:'document_sha256',type:'string',length:64)] public string $documentSha256; #[ORM\Column(type:'string',length:16)] public string $status; #[ORM\Column(type:'integer')] public int $progress; #[ORM\Column(name:'error_message',type:'string',length:500,nullable:true)] public ?string $errorMessage=null; #[ORM\Column(name:'created_at',type:'datetime_immutable')] public DateTimeImmutable $createdAt; #[ORM\Column(name:'started_at',type:'datetime_immutable',nullable:true)] public ?DateTimeImmutable $startedAt=null; #[ORM\Column(name:'finished_at',type:'datetime_immutable',nullable:true)] public ?DateTimeImmutable $finishedAt=null; }
