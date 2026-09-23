<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'question_import_rows')]
class QuestionImportRowRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    public ?int $id = null;

    #[ORM\Column(name: 'import_id', type: 'string', length: 36)]
    public string $importId;

    #[ORM\Column(name: 'line_number', type: 'integer')]
    public int $lineNumber;

    #[ORM\Column(type: 'json')]
    public array $payload;

    #[ORM\Column(type: 'string', length: 16)]
    public string $status;

    #[ORM\Column(type: 'json')]
    public array $errors;

    #[ORM\Column(name: 'question_id', type: 'string', length: 36, nullable: true)]
    public ?string $questionId = null;
}
