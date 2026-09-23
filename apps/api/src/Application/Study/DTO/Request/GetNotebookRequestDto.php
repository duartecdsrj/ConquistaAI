<?php
declare(strict_types=1);

namespace App\Application\Study\DTO\Request;

final readonly class GetNotebookRequestDto
{
    public function __construct(public string $id)
    {
        if (trim($id) === '') {
            throw new \InvalidArgumentException('Identificador de caderno invalido.');
        }
    }
}
