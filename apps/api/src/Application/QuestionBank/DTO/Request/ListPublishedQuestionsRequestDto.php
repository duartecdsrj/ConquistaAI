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
        public ?string $content = null,
    ) {
        if ($page < 1 || $perPage < 1 || $perPage > 100) {
            throw new \InvalidArgumentException('Paginacao invalida.');
        }
        if ($difficulty !== null && !in_array($difficulty, ['EASY', 'MEDIUM', 'HARD'], true)) {
            throw new \InvalidArgumentException('Dificuldade invalida.');
        }
        if ($content !== null && mb_strlen($content) > 200) {
            throw new \InvalidArgumentException('Busca textual muito longa.');
        }
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
