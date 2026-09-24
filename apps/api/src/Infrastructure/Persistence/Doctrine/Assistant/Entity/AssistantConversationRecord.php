<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Assistant\Entity;
use DateTimeImmutable;use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name:'assistant_conversations')]
class AssistantConversationRecord { #[ORM\Id] #[ORM\Column(type:'string',length:36)] public string $id; #[ORM\Column(name:'user_id',type:'string',length:36)] public string $userId; #[ORM\Column(name:'syllabus_id',type:'string',length:36)] public string $syllabusId; #[ORM\Column(type:'string',length:160)] public string $title; #[ORM\Column(name:'created_at',type:'datetime_immutable')] public DateTimeImmutable $createdAt; #[ORM\Column(name:'updated_at',type:'datetime_immutable')] public DateTimeImmutable $updatedAt; }
