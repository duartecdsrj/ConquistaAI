<?php
declare(strict_types=1);

namespace Tests\Unit\Study;

use App\Application\Study\DTO\Request\GetNotebookRequestDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Application\Study\Port\TransactionManagerInterface;
use App\Application\Study\Service\FinishNotebookService;
use App\Application\Study\Service\StartNotebookService;
use App\Domain\Study\Entity\Notebook;
use App\Domain\Study\Enum\NotebookMode;
use App\Domain\Study\Enum\NotebookStatus;
use App\Domain\Study\Repository\NotebookRepositoryInterface;
use App\Domain\Study\ValueObject\FrozenQuestionSelection;
use PHPUnit\Framework\TestCase;

final class NotebookExecutionServiceTest extends TestCase
{
    public function testStartsAndFinishesNotebookPersistingItsExecutionState(): void
    {
        $notebook = new Notebook('n1', 'u1', 'Caderno', NotebookMode::STUDY, FrozenQuestionSelection::fromQuestionIds(['q1'], 1), new \DateTimeImmutable('2026-01-01T00:00:00Z'));
        $repository = new class($notebook) implements NotebookRepositoryInterface {
            public function __construct(public Notebook $notebook) {}
            public function save(Notebook $notebook): void { $this->notebook = $notebook; }
            public function findByIdForUser(string $id, string $userId): ?Notebook { return $id === 'n1' && $userId === 'u1' ? $this->notebook : null; }
            public function listForUser(string $userId, int $offset, int $limit): array { return []; }
            public function countForUser(string $userId): int { return 0; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };
        $mapper = new NotebookResponseMapper();

        $started = (new StartNotebookService($repository, $mapper, $transactions))
            ->startForUser('u1', new GetNotebookRequestDto('n1'));

        self::assertSame(NotebookStatus::IN_PROGRESS->value, $started?->status);
        self::assertNotNull($started?->startedAt);

        $finished = (new FinishNotebookService($repository, $mapper, $transactions))
            ->finishForUser('u1', new GetNotebookRequestDto('n1'));

        self::assertSame(NotebookStatus::FINISHED->value, $finished?->status);
        self::assertNotNull($finished?->finishedAt);
        self::assertNotNull($finished?->durationSeconds);
    }

    public function testDoesNotRestartFinishedNotebook(): void
    {
        $notebook = new Notebook('n1', 'u1', 'Caderno', NotebookMode::STUDY, FrozenQuestionSelection::fromQuestionIds(['q1'], 1), new \DateTimeImmutable('2026-01-01T00:00:00Z'));
        $notebook->finish(new \DateTimeImmutable('2026-01-01T00:01:00Z'));
        $repository = new class($notebook) implements NotebookRepositoryInterface {
            public function __construct(private Notebook $notebook) {}
            public function save(Notebook $notebook): void {}
            public function findByIdForUser(string $id, string $userId): ?Notebook { return $this->notebook; }
            public function listForUser(string $userId, int $offset, int $limit): array { return []; }
            public function countForUser(string $userId): int { return 0; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $this->expectException(\DomainException::class);
        (new StartNotebookService($repository, new NotebookResponseMapper(), $transactions))
            ->startForUser('u1', new GetNotebookRequestDto('n1'));
    }
}
