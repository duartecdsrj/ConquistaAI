<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Study\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'notebook_questions')]
class NotebookQuestionRecord
{
    #[ORM\Id]
    #[ORM\Column(name: 'notebook_id', type: 'string', length: 36)]
    public string $notebookId;
    #[ORM\Id]
    #[ORM\Column(name: 'question_id', type: 'string', length: 36)]
    public string $questionId;
    #[ORM\Column(type: 'integer')]
    public int $position;
}
