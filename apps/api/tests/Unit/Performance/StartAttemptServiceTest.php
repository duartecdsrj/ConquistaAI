<?php
declare(strict_types=1);

namespace Tests\Unit\Performance;

use App\Application\Performance\DTO\Request\StartAttemptRequestDto;
use App\Application\Performance\Port\TransactionManagerInterface;
use App\Application\Performance\Service\StartAttemptService;
use App\Domain\Performance\Entity\Answer;
use App\Domain\Performance\Entity\Attempt;
use App\Domain\Performance\Repository\AttemptRepositoryInterface;
use App\Domain\Study\Entity\Notebook;
use App\Domain\Study\Enum\NotebookMode;
use App\Domain\Study\Repository\NotebookRepositoryInterface;
use App\Domain\Study\ValueObject\FrozenQuestionSelection;
use PHPUnit\Framework\TestCase;

final class StartAttemptServiceTest extends TestCase
{
    public function testStartsOnlyAQuestionFrozenInTheUsersNotebook(): void
    {
        $notebook = new Notebook(
            'notebook-1',
            'user-1',
            'Caderno',
            NotebookMode::STUDY,
            FrozenQuestionSelection::fromQuestionIds(['question-1'], 1),
            new \DateTimeImmutable('2026-01-01'),
        );
        $notebooks = new class($notebook) implements NotebookRepositoryInterface {
            public function __construct(private readonly Notebook $notebook) {}
            public function save(Notebook $notebook): void {}
            public function findByIdForUser(string $id, string $userId): ?Notebook {
                return $id === $this->notebook->id && $userId === $this->notebook->userId ? $this->notebook : null;
            }
            public function listForUser(string $userId, int $offset, int $limit): array { return []; }
            public function countForUser(string $userId): int { return 0; }
        };
        $attempts = new class implements AttemptRepositoryInterface {
            public ?Attempt $saved = null;
            public function saveAttempt(Attempt $attempt): void { $this->saved = $attempt; }
            public function nextNumber(string $userId, string $notebookId, string $questionId): int { return 2; }
            public function appendAnswer(Answer $answer): void {}
            public function listAnswers(string $attemptId): array { return []; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $attempt = (new StartAttemptService($notebooks, $attempts, $transactions))
            ->start(new StartAttemptRequestDto('user-1', 'notebook-1', 'question-1', 'STUDY'));

        self::assertSame(2, $attempt->number);
        self::assertSame('question-1', $attempts->saved?->questionId);
    }

    public function testRejectsQuestionOutsideFrozenSelection(): void
    {
        $notebooks = new class implements NotebookRepositoryInterface {
            public function save(Notebook $notebook): void {}
            public function findByIdForUser(string $id, string $userId): ?Notebook {
                return new Notebook('notebook-1', 'user-1', 'Caderno', NotebookMode::STUDY, FrozenQuestionSelection::fromQuestionIds(['question-1'], 1), new \DateTimeImmutable());
            }
            public function listForUser(string $userId, int $offset, int $limit): array { return []; }
            public function countForUser(string $userId): int { return 0; }
        };
        $attempts = new class implements AttemptRepositoryInterface {
            public function saveAttempt(Attempt $attempt): void {}
            public function nextNumber(string $userId, string $notebookId, string $questionId): int { return 1; }
            public function appendAnswer(Answer $answer): void {}
            public function listAnswers(string $attemptId): array { return []; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $this->expectException(\DomainException::class);
        (new StartAttemptService($notebooks, $attempts, $transactions))
            ->start(new StartAttemptRequestDto('user-1', 'notebook-1', 'question-2', 'STUDY'));
    }
}
