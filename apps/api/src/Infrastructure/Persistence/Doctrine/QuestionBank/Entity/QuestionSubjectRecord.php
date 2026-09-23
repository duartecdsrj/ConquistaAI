<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_subjects')]
class QuestionSubjectRecord
{
    #[ORM\Id]
    #[ORM\Column(name: 'question_id', type: 'string', length: 36)]
    public string $questionId;
    #[ORM\Id]
    #[ORM\Column(name: 'subject_id', type: 'string', length: 36)]
    public string $subjectId;
}
