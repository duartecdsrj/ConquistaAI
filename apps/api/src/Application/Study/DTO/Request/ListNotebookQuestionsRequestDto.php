<?php
declare(strict_types=1);

namespace App\Application\Study\DTO\Request;

final readonly class ListNotebookQuestionsRequestDto
{
    public function __construct(
        public string $notebookId,
        public int $page,
        public int $perPage,
    ) {
        if (trim($notebookId) === '' || $page < 1 || $perPage < 1 || $perPage > 100) {
            throw new \InvalidArgumentException('Parametros de navegacao de caderno invalidos.');
        }
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
