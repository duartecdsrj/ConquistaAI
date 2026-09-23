<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Service;

use App\Application\QuestionBank\DTO\Request\ListPublishedQuestionsRequestDto;
use App\Application\QuestionBank\Mapper\PublishedQuestionResponseMapper;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionFilter;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionPage;
use App\Domain\QuestionBank\Repository\PublishedQuestionRepositoryInterface;

final class ListPublishedQuestionsService
{
    public function __construct(
        private readonly PublishedQuestionRepositoryInterface $questions,
        private readonly PublishedQuestionResponseMapper $mapper,
    ) {
    }

    /** @return array{items:list<object>,total:int,page:int,perPage:int} */
    public function list(ListPublishedQuestionsRequestDto $request): array
    {
        $page = $this->questions->findPublished(new PublishedQuestionFilter(
            $request->offset(),
            $request->perPage,
            $request->subjectId,
            $request->board,
            $request->year,
            $request->difficulty,
        ));

        return [
            'items' => array_map($this->mapper->toResponse(...), $page->items),
            'total' => $page->total,
            'page' => $request->page,
            'perPage' => $request->perPage,
        ];
    }
}
