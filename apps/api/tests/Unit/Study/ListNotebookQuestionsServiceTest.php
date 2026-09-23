<?php
declare(strict_types=1);

namespace Tests\Unit\Study;

use App\Application\QuestionBank\Mapper\PublishedQuestionResponseMapper;
use App\Application\Study\DTO\Request\ListNotebookQuestionsRequestDto;
use App\Application\Study\Service\ListNotebookQuestionsService;
use App\Domain\QuestionBank\ReadModel\PublishedQuestion;
use App\Domain\QuestionBank\Repository\FrozenQuestionReaderInterface;
use App\Domain\Study\Entity\Notebook;
use App\Domain\Study\Enum\NotebookMode;
use App\Domain\Study\Repository\NotebookRepositoryInterface;
use App\Domain\Study\ValueObject\FrozenQuestionSelection;
use PHPUnit\Framework\TestCase;

final class ListNotebookQuestionsServiceTest extends TestCase
{
    public function testReturnsFrozenQuestionsInSelectionOrderAndPaginates(): void
    {
        $notebook = new Notebook('n1', 'u1', 'Caderno', NotebookMode::STUDY, FrozenQuestionSelection::fromQuestionIds(['q2', 'q1', 'q3'], 3), new \DateTimeImmutable('2026-01-01T00:00:00Z'));
        $notebooks = new class($notebook) implements NotebookRepositoryInterface {
            public function __construct(private Notebook $notebook) {}
            public function save(Notebook $notebook): void {}
            public function findByIdForUser(string $id, string $userId): ?Notebook { return $id === 'n1' && $userId === 'u1' ? $this->notebook : null; }
            public function listForUser(string $userId, int $offset, int $limit): array { return []; }
            public function countForUser(string $userId): int { return 0; }
        };
        $questions = new class implements FrozenQuestionReaderInterface {
            public function findByIds(array $ids): array {
                return [
                    new PublishedQuestion('q1', 'Primeira', 'EASY', null, null, []),
                    new PublishedQuestion('q2', 'Segunda', 'MEDIUM', null, null, []),
                    new PublishedQuestion('q3', 'Terceira', 'HARD', null, null, []),
                ];
            }
        };

        $page = (new ListNotebookQuestionsService($notebooks, $questions, new PublishedQuestionResponseMapper()))
            ->listForUser('u1', new ListNotebookQuestionsRequestDto('n1', 2, 1));

        self::assertNotNull($page);
        self::assertSame(3, $page->total);
        self::assertSame('Primeira', $page->items[0]->statement);
    }

    public function testDoesNotExposeNotebookFromAnotherUser(): void
    {
        $notebooks = new class implements NotebookRepositoryInterface {
            public function save(Notebook $notebook): void {}
            public function findByIdForUser(string $id, string $userId): ?Notebook { return null; }
            public function listForUser(string $userId, int $offset, int $limit): array { return []; }
            public function countForUser(string $userId): int { return 0; }
        };
        $questions = new class implements FrozenQuestionReaderInterface { public function findByIds(array $ids): array { return []; } };

        self::assertNull((new ListNotebookQuestionsService($notebooks, $questions, new PublishedQuestionResponseMapper()))
            ->listForUser('other', new ListNotebookQuestionsRequestDto('n1', 1, 25)));
    }
}
