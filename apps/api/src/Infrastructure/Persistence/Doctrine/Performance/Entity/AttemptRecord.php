<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Performance\Entity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name: 'attempts')]
class AttemptRecord {
 #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
 #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId;
 #[ORM\Column(name: 'notebook_id', type: 'string', length: 36)] public string $notebookId;
 #[ORM\Column(name: 'question_id', type: 'string', length: 36)] public string $questionId;
 #[ORM\Column(type: 'integer')] public int $number;
 #[ORM\Column(name: 'started_at', type: 'datetime_immutable')] public DateTimeImmutable $startedAt;
 #[ORM\Column(name: 'completed_at', type: 'datetime_immutable', nullable: true)] public ?DateTimeImmutable $completedAt = null;
 #[ORM\Column(type: 'string', length: 16)] public string $context;
 #[ORM\Column(name: 'final_answer_id', type: 'string', length: 36, nullable: true)] public ?string $finalAnswerId = null;
 #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
}
