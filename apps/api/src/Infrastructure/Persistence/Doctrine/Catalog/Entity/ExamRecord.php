<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Catalog\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'exams')]
class ExamRecord
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id;

    #[ORM\Column(type: 'string', length: 190)]
    public string $name;

    #[ORM\Column(type: 'string', length: 190, nullable: true)]
    public ?string $organizer;

    #[ORM\Column(type: 'smallint', nullable: true)]
    public ?int $year;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    public DateTimeImmutable $updatedAt;
}
