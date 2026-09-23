<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'questions')]
class QuestionRecord
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id;
    #[ORM\Column(name: 'syllabus_id', type: 'string', length: 36)]
    public string $syllabusId;
    #[ORM\Column(type: 'text')]
    public string $statement;
    #[ORM\Column(type: 'string', length: 16)]
    public string $difficulty;
    #[ORM\Column(type: 'string', length: 190, nullable: true)]
    public ?string $board;
    #[ORM\Column(name: 'exam_year', type: 'smallint', nullable: true)]
    public ?int $examYear;
    #[ORM\Column(type: 'string', length: 16)]
    public string $status;
    #[ORM\Column(name: 'correct_option_id', type: 'string', length: 36, nullable: true)]
    public ?string $correctOptionId = null;
}
