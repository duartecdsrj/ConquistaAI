<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Study\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'notebooks')]
class NotebookRecord
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id;
    #[ORM\Column(name: 'user_id', type: 'string', length: 36)]
    public string $userId;
    #[ORM\Column(type: 'string', length: 190)]
    public string $name;
    #[ORM\Column(type: 'string', length: 16)]
    public string $type;
    #[ORM\Column(type: 'string', length: 16)]
    public string $mode;
    #[ORM\Column(type: 'string', length: 16)]
    public string $status;
    #[ORM\Column(type: 'json')]
    public array $filters = [];
    #[ORM\Column(name: 'duration_seconds', type: 'integer', nullable: true)]
    public ?int $durationSeconds = null;
    #[ORM\Column(name: 'started_at', type: 'datetime_immutable', nullable: true)]
    public ?DateTimeImmutable $startedAt = null;
    #[ORM\Column(name: 'finished_at', type: 'datetime_immutable', nullable: true)]
    public ?DateTimeImmutable $finishedAt = null;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    public DateTimeImmutable $updatedAt;
}
