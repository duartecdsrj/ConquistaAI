<?php
declare(strict_types=1);

namespace App\Application\Study\Service;

use App\Application\QuestionBank\Mapper\PublishedQuestionResponseMapper;
use App\Application\Study\DTO\Request\ListNotebookQuestionsRequestDto;
use App\Application\Study\DTO\Response\PaginatedNotebookQuestionsResponseDto;
use App\Domain\QuestionBank\Repository\FrozenQuestionReaderInterface;
use App\Domain\Study\Repository\NotebookRepositoryInterface;

final class ListNotebookQuestionsService
{
    public function __construct(
        private readonly NotebookRepositoryInterface $notebooks,
        private readonly FrozenQuestionReaderInterface $questions,
        private readonly PublishedQuestionResponseMapper $mapper,
    ) {}

    public function listForUser(string $userId, ListNotebookQuestionsRequestDto $request): ?PaginatedNotebookQuestionsResponseDto
    {
        $notebook = $this->notebooks->findByIdForUser($request->notebookId, $userId);
        if ($notebook === null) {
            return null;
        }

        $ids = $notebook->selection->questionIds;
        $questionsById = [];
        foreach ($this->questions->findByIds($ids) as $question) {
            $questionsById[$question->id] = $question;
        }
        $ordered = array_values(array_filter($ids, static fn (string $id): bool => isset($questionsById[$id])));
        $window = array_slice($ordered, $request->offset(), $request->perPage);

        return new PaginatedNotebookQuestionsResponseDto(
            array_map(fn (string $id) => $this->mapper->toResponse($questionsById[$id]), $window),
            $request->page,
            $request->perPage,
            count($ordered),
        );
    }
}
