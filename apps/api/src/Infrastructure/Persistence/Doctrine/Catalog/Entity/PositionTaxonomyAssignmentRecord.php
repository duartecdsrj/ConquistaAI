<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Catalog\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'position_taxonomy_subjects')]
class PositionTaxonomyAssignmentRecord
{
    #[ORM\Id]
    #[ORM\Column(name: 'position_id', type: 'string', length: 36)]
    public string $positionId;

    #[ORM\Id]
    #[ORM\Column(name: 'taxonomy_subject_id', type: 'string', length: 36)]
    public string $taxonomySubjectId;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;
}
