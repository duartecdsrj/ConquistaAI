<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\Catalog\Entity;
use DateTimeImmutable; use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity] #[ORM\Table(name: 'syllabi')]
class SyllabusRecord {
 #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
 #[ORM\Column(name: 'exam_position_id', type: 'string', length: 36)] public string $positionId;
 #[ORM\Column(type: 'string', length: 190)] public string $name;
 #[ORM\Column(name: 'published_at', type: 'date_immutable', nullable: true)] public ?DateTimeImmutable $publishedAt;
 #[ORM\Column(name: 'source_url', type: 'string', length: 2048, nullable: true)] public ?string $sourceUrl;
 #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
 #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public DateTimeImmutable $updatedAt;
}
