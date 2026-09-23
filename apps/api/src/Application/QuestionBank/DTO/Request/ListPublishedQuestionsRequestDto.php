<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Request;

final readonly class ListPublishedQuestionsRequestDto
{
    public function __construct(
        public int $page,
        public int $perPage,
        public ?string $subjectId,
        public ?string $board,
        public ?int $year,
        public ?string $difficulty,
    ) {
        if ($page < 1 || $perPage < 1 || $perPage > 100) {
            throw new \InvalidArgumentException('Paginacao invalida.');
        }
        if ($difficulty !== null && !in_array($difficulty, ['EASY', 'MEDIUM', 'HARD'], true)) {
            throw new \InvalidArgumentException('Dificuldade invalida.');
        }
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
