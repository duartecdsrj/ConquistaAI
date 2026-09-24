<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Assistant\Entity;
use DateTimeImmutable;use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name:'assistant_messages')]
class AssistantMessageRecord { #[ORM\Id] #[ORM\Column(type:'string',length:36)] public string $id; #[ORM\Column(name:'conversation_id',type:'string',length:36)] public string $conversationId; #[ORM\Column(type:'string',length:12)] public string $role; #[ORM\Column(type:'text')] public string $content; #[ORM\Column(type:'string',length:80,nullable:true)] public ?string $provider=null; #[ORM\Column(type:'string',length:120,nullable:true)] public ?string $model=null; #[ORM\Column(name:'evidence_json',type:'json',nullable:true)] public ?array $evidenceJson=null; #[ORM\Column(name:'created_at',type:'datetime_immutable')] public DateTimeImmutable $createdAt; }
