<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'study_map_schedule_dependencies')]
class StudyMapScheduleDependencyRecord
{
    #[ORM\Id] #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId;
    #[ORM\Id] #[ORM\Column(name: 'exam_id', type: 'string', length: 36)] public string $examId;
    #[ORM\Id] #[ORM\Column(name: 'taxonomy_subject_id', type: 'string', length: 36)] public string $taxonomySubjectId;
    #[ORM\Id] #[ORM\Column(name: 'predecessor_subject_id', type: 'string', length: 36)] public string $predecessorSubjectId;
}
