<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Response;

final readonly class PublishedQuestionResponseDto
{
    /** @param list<PublishedQuestionOptionResponseDto> $options */
    public function __construct(
        public string $id,
        public string $statement,
        public string $difficulty,
        public ?string $board,
        public ?int $year,
        public array $options,
    ) {
    }
}
