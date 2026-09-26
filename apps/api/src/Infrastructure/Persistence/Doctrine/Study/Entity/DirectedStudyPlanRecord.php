<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Study\Entity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name:'directed_study_plans')] class DirectedStudyPlanRecord { #[ORM\Id] #[ORM\Column(type:'string',length:36)] public string $id; #[ORM\Column(name:'user_id',type:'string',length:36)] public string $userId; #[ORM\Column(name:'exam_id',type:'string',length:36)] public string $examId; #[ORM\Column(name:'position_id',type:'string',length:36)] public string $positionId; #[ORM\Column(type:'string',length:190)] public string $name; #[ORM\Column(name:'created_at',type:'datetime_immutable')] public \DateTimeImmutable $createdAt; #[ORM\Column(name:'updated_at',type:'datetime_immutable')] public \DateTimeImmutable $updatedAt; }
