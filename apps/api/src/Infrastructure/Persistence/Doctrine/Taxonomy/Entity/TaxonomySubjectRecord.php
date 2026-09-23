<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Taxonomy\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'taxonomy_subjects')]
class TaxonomySubjectRecord
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id;

    #[ORM\Column(name: 'parent_id', type: 'string', length: 36, nullable: true)]
    public ?string $parentId = null;

    #[ORM\Column(type: 'string', length: 190)]
    public string $name;

    #[ORM\Column(type: 'string', length: 190)]
    public string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: 'integer')]
    public int $level;

    #[ORM\Column(type: 'boolean')]
    public bool $active = true;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    public DateTimeImmutable $updatedAt;
}
