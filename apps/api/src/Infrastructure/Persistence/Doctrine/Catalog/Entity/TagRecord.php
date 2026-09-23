<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Catalog\Entity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name: 'tags')]
class TagRecord {
 #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
 #[ORM\Column(type: 'string', length: 100, unique: true)] public string $name;
 #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
}
