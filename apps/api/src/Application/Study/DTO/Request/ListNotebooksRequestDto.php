<?php
declare(strict_types=1);

namespace App\Application\Study\DTO\Request;

final readonly class ListNotebooksRequestDto
{
    public function __construct(
        public int $page,
        public int $perPage,
    ) {
        if ($page < 1 || $perPage < 1 || $perPage > 100) {
            throw new \InvalidArgumentException('Paginacao invalida.');
        }
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
