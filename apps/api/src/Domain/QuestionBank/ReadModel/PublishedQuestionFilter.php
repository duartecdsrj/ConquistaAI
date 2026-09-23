<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ReadModel;

final readonly class PublishedQuestionFilter
{
    public function __construct(
        public int $offset,
        public int $limit,
        public ?string $subjectId,
        public ?string $board,
        public ?int $year,
        public ?string $difficulty,
    ) {
    }
}
