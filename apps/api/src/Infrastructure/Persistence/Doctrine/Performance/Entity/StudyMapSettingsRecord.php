<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'study_map_settings')]
class StudyMapSettingsRecord
{
    #[ORM\Id] #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId;
    #[ORM\Id] #[ORM\Column(name: 'exam_id', type: 'string', length: 36)] public string $examId;
    #[ORM\Column(name: 'exam_date', type: 'date_immutable', nullable: true)] public ?DateTimeImmutable $examDate = null;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public DateTimeImmutable $updatedAt;
}
