<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Catalog\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subject_taxonomy_assignments')]
class SubjectTaxonomyAssignmentRecord
{
    #[ORM\Id] #[ORM\Column(name: 'subject_id', type: 'string', length: 36)] public string $subjectId;
    #[ORM\Id] #[ORM\Column(name: 'taxonomy_subject_id', type: 'string', length: 36)] public string $taxonomySubjectId;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
}
