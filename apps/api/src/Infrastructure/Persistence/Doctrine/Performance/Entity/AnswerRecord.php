<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Performance\Entity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name: 'answers')]
class AnswerRecord {
 #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
 #[ORM\Column(name: 'attempt_id', type: 'string', length: 36)] public string $attemptId;
 #[ORM\Column(name: 'option_id', type: 'string', length: 36, nullable: true)] public ?string $optionId = null;
 #[ORM\Column(type: 'integer')] public int $sequence;
 #[ORM\Column(name: 'submitted_at', type: 'datetime_immutable')] public DateTimeImmutable $submittedAt;
 #[ORM\Column(name: 'elapsed_seconds', type: 'integer')] public int $elapsedSeconds;
 #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
}
