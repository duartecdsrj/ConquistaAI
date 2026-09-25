<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Catalog\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subjects')]
class SubjectRecord
{
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'syllabus_id', type: 'string', length: 36)] public string $syllabusId;
    #[ORM\Column(name: 'parent_id', type: 'string', length: 36, nullable: true)] public ?string $parentId;
    #[ORM\Column(type: 'string', length: 190)] public string $name;
    #[ORM\Column(name: 'source_excerpt', type: 'text', nullable: true)] public ?string $sourceExcerpt = null;
    #[ORM\Column(name: 'source_page', type: 'integer', nullable: true)] public ?int $sourcePage = null;
    #[ORM\Column(name: 'source_start_offset', type: 'integer', nullable: true)] public ?int $sourceStartOffset = null;
    #[ORM\Column(name: 'source_end_offset', type: 'integer', nullable: true)] public ?int $sourceEndOffset = null;
    #[ORM\Column(name: 'sort_order', type: 'integer')] public int $sortOrder;
    #[ORM\Column(name: 'selection_weight', type: 'decimal', precision: 8, scale: 4)] public string $selectionWeight = '1.0000';
    #[ORM\Column(name: 'weight_source', type: 'string', length: 16)] public string $weightSource = 'DEFAULT';
    #[ORM\Column(name: 'weight_confidence', type: 'decimal', precision: 5, scale: 4)] public string $weightConfidence = '0.0000';
    #[ORM\Column(name: 'weight_calculated_at', type: 'datetime_immutable', nullable: true)] public ?DateTimeImmutable $weightCalculatedAt = null;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public DateTimeImmutable $updatedAt;
}
