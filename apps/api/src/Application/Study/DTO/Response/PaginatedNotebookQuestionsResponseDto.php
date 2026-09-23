<?php
declare(strict_types=1);

namespace App\Application\Study\DTO\Response;

use App\Application\QuestionBank\DTO\Response\PublishedQuestionResponseDto;

final readonly class PaginatedNotebookQuestionsResponseDto
{
    /** @param list<PublishedQuestionResponseDto> $items */
    public function __construct(public array $items, public int $page, public int $perPage, public int $total) {}
}
