<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_options')]
class QuestionOptionRecord
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id;
    #[ORM\Column(name: 'question_id', type: 'string', length: 36)]
    public string $questionId;
    #[ORM\Column(type: 'string', length: 1)]
    public string $label;
    #[ORM\Column(type: 'text')]
    public string $content;
    #[ORM\Column(name: 'sort_order', type: 'smallint')]
    public int $sortOrder;
}
