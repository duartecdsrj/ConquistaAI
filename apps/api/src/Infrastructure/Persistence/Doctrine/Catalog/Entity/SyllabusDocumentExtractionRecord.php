<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Catalog\Entity;
use DateTimeImmutable;use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name:'syllabus_document_extractions')]
class SyllabusDocumentExtractionRecord { #[ORM\Id] #[ORM\Column(type:'string',length:36)] public string $id; #[ORM\Column(name:'syllabus_id',type:'string',length:36)] public string $syllabusId; #[ORM\Column(name:'document_sha256',type:'string',length:64)] public string $documentSha256; #[ORM\Column(name:'page_number',type:'integer')] public int $pageNumber; #[ORM\Column(name:'text_content',type:'text')] public string $textContent; #[ORM\Column(name:'start_offset',type:'integer')] public int $startOffset; #[ORM\Column(name:'end_offset',type:'integer')] public int $endOffset; #[ORM\Column(name:'created_at',type:'datetime_immutable')] public DateTimeImmutable $createdAt; #[ORM\Column(name:'updated_at',type:'datetime_immutable')] public DateTimeImmutable $updatedAt; }
