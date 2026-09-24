<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'questions')]
class QuestionRecord
{
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'syllabus_id', type: 'string', length: 36)] public string $syllabusId;
    #[ORM\Column(type: 'text')] public string $statement;
    #[ORM\Column(type: 'string', length: 16)] public string $difficulty;
    #[ORM\Column(type: 'string', length: 190, nullable: true)] public ?string $board;
    #[ORM\Column(name: 'exam_year', type: 'smallint', nullable: true)] public ?int $examYear;
    #[ORM\Column(type: 'string', length: 255, nullable: true)] public ?string $source = null;
    #[ORM\Column(name: 'reference_url', type: 'string', length: 2048, nullable: true)] public ?string $referenceUrl = null;
    #[ORM\Column(type: 'string', length: 16)] public string $origin = 'EXAM';
    #[ORM\Column(type: 'string', length: 16)] public string $status;
    #[ORM\Column(name: 'correct_option_id', type: 'string', length: 36, nullable: true)] public ?string $correctOptionId = null;
    #[ORM\Column(name: 'created_by', type: 'string', length: 36)] public string $createdBy;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public \DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public \DateTimeImmutable $updatedAt;
}
