<?php
declare(strict_types=1);

namespace App\Application\Study\Service;

use App\Application\Study\DTO\Request\CreateNotebookRequestDto;
use App\Application\Study\DTO\Response\NotebookResponseDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Application\Study\Port\TransactionManagerInterface;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionFilter;
use App\Domain\QuestionBank\Repository\PublishedQuestionRepositoryInterface;
use App\Domain\Study\Entity\Notebook;
use App\Domain\Study\Enum\NotebookMode;
use App\Domain\Study\Repository\NotebookRepositoryInterface;
use App\Domain\Study\ValueObject\FrozenQuestionSelection;

final class CreateNotebookService
{
    public function __construct(
        private readonly NotebookRepositoryInterface $notebooks,
        private readonly PublishedQuestionRepositoryInterface $questions,
        private readonly NotebookResponseMapper $mapper,
        private readonly TransactionManagerInterface $transactions,
        private readonly \DateTimeZone $utc = new \DateTimeZone('UTC'),
    ) {
    }

    public function create(CreateNotebookRequestDto $request): NotebookResponseDto
    {
        return $this->transactions->transactional(function () use ($request): NotebookResponseDto {
            $page = $this->questions->findPublished(new PublishedQuestionFilter(
                0,
                $request->quantity,
                $request->filters['subject_id'] ?? null,
                $request->filters['board'] ?? null,
                $request->filters['year'] ?? null,
                $request->filters['difficulty'] ?? null,
                $request->filters['syllabus_id'] ?? null,
            ));
            $selection = FrozenQuestionSelection::fromQuestionIds(
                array_map(static fn ($question): string => $question->id, $page->items),
                $request->quantity,
            );
            $notebook = Notebook::create(
                $request->userId,
                $request->name,
                NotebookMode::from($request->mode),
                $selection,
                new \DateTimeImmutable('now', $this->utc),
            );
            $this->notebooks->save($notebook);

            return $this->mapper->toResponse($notebook);
        });
    }
}
