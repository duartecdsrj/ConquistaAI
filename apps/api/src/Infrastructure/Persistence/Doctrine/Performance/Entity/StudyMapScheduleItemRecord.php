<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'study_map_schedule_items')]
class StudyMapScheduleItemRecord
{
    #[ORM\Id] #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId;
    #[ORM\Id] #[ORM\Column(name: 'exam_id', type: 'string', length: 36)] public string $examId;
    #[ORM\Id] #[ORM\Column(name: 'taxonomy_subject_id', type: 'string', length: 36)] public string $taxonomySubjectId;
    #[ORM\Column(name: 'start_date', type: 'date_immutable')] public DateTimeImmutable $startDate;
    #[ORM\Column(name: 'end_date', type: 'date_immutable')] public DateTimeImmutable $endDate;
    #[ORM\Column(name: 'estimated_minutes', type: 'integer', nullable: true)] public ?int $estimatedMinutes = null;
    #[ORM\Column(type: 'string', length: 16)] public string $status;
    #[ORM\Column(name: 'completed_at', type: 'datetime_immutable', nullable: true)] public ?DateTimeImmutable $completedAt = null;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public DateTimeImmutable $updatedAt;
}
