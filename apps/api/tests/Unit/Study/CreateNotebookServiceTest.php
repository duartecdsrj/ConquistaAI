<?php
declare(strict_types=1);

namespace Tests\Unit\Study;

use App\Application\Study\DTO\Request\CreateNotebookRequestDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Application\Study\Port\TransactionManagerInterface;
use App\Application\Study\Service\CreateNotebookService;
use App\Domain\QuestionBank\ReadModel\PublishedQuestion;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionFilter;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionPage;
use App\Domain\QuestionBank\Repository\PublishedQuestionRepositoryInterface;
use App\Domain\Study\Entity\Notebook;
use App\Domain\Study\Repository\NotebookRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class CreateNotebookServiceTest extends TestCase
{
    public function testFreezesPublishedQuestionsSelectedByFilters(): void
    {
        $repository = new class implements NotebookRepositoryInterface {
            public ?Notebook $saved = null;
            public function save(Notebook $notebook): void { $this->saved = $notebook; }
            public function findByIdForUser(string $id, string $userId): ?Notebook { return null; }
            public function listForUser(string $userId, int $offset, int $limit): array { return []; }
            public function countForUser(string $userId): int { return 0; }
        };
        $questions = new class implements PublishedQuestionRepositoryInterface {
            public ?PublishedQuestionFilter $filter = null;
            public function findPublished(PublishedQuestionFilter $filter): PublishedQuestionPage
            {
                $this->filter = $filter;

                return new PublishedQuestionPage([
                    new PublishedQuestion('q1', 'Primeira', 'EASY', 'Banca', 2026, []),
                    new PublishedQuestion('q2', 'Segunda', 'EASY', 'Banca', 2026, []),
                ], 2);
            }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $response = (new CreateNotebookService($repository, $questions, new NotebookResponseMapper(), $transactions))
            ->create(new CreateNotebookRequestDto('user', 'Nome', 'STUDY', 2, ['board' => 'Banca', 'difficulty' => 'EASY']));

        self::assertSame(['q1', 'q2'], $response->questionIds);
        self::assertSame(['q1', 'q2'], $repository->saved?->selection->questionIds);
        self::assertSame(2, $questions->filter?->limit);
        self::assertSame('Banca', $questions->filter?->board);
        self::assertSame('EASY', $questions->filter?->difficulty);
    }

    public function testRejectsNotebookWhenFiltersDoNotFindEnoughPublishedQuestions(): void
    {
        $repository = new class implements NotebookRepositoryInterface {
            public function save(Notebook $notebook): void {}
            public function findByIdForUser(string $id, string $userId): ?Notebook { return null; }
            public function listForUser(string $userId, int $offset, int $limit): array { return []; }
            public function countForUser(string $userId): int { return 0; }
        };
        $questions = new class implements PublishedQuestionRepositoryInterface {
            public function findPublished(PublishedQuestionFilter $filter): PublishedQuestionPage
            {
                return new PublishedQuestionPage([new PublishedQuestion('q1', 'Primeira', 'EASY', null, null, [])], 1);
            }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $this->expectException(\DomainException::class);
        (new CreateNotebookService($repository, $questions, new NotebookResponseMapper(), $transactions))
            ->create(new CreateNotebookRequestDto('user', 'Nome', 'EXAM', 2, []));
    }
}
