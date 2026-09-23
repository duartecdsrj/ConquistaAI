<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_imports')]
class QuestionImportRecord
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $id;

    #[ORM\Column(name: 'created_by', type: 'string', length: 36)]
    public string $createdBy;

    #[ORM\Column(type: 'string', length: 8)]
    public string $format;

    #[ORM\Column(type: 'string', length: 255)]
    public string $filename;

    #[ORM\Column(type: 'string', length: 16)]
    public string $status;

    #[ORM\Column(type: 'json')]
    public array $totals;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;
}
