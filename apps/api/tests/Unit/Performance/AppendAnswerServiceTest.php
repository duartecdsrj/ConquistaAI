<?php
declare(strict_types=1);

namespace Tests\Unit\Performance;

use App\Application\Performance\DTO\Request\AppendAnswerRequestDto;
use App\Application\Performance\Port\TransactionManagerInterface;
use App\Application\Performance\Service\AppendAnswerService;
use App\Domain\Performance\Entity\Answer;
use App\Domain\Performance\Entity\Attempt;
use App\Domain\Performance\Repository\AttemptRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class AppendAnswerServiceTest extends TestCase
{
    public function testItAppendsAnImmutableAnswerWithServerSequence(): void
    {
        $repository = new class implements AttemptRepositoryInterface {
            public ?Answer $answer = null;
            public function saveAttempt(Attempt $attempt): void {}
            public function findByIdForUser(string $attemptId, string $userId): ?Attempt {
                return new Attempt('attempt', $userId, 'notebook', 'question', 1, 'STUDY', new \DateTimeImmutable());
            }
            public function nextNumber(string $userId, string $notebookId, string $questionId): int { return 1; }
            public function complete(string $attemptId, string $finalAnswerId, \DateTimeImmutable $completedAt): bool { return true; }
            public function appendAnswer(Answer $answer): void { $this->answer = $answer; }
            public function listAnswers(string $attemptId): array { return [new Answer('old', $attemptId, 'old-option', 1, 1, new \DateTimeImmutable())]; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $answer = (new AppendAnswerService($repository, $transactions))
            ->append(new AppendAnswerRequestDto('user', 'attempt', 'option', 4));

        self::assertSame($answer, $repository->answer);
        self::assertSame(2, $answer->sequence);
    }

    public function testItRejectsAttemptOutsideCurrentUser(): void
    {
        $repository = new class implements AttemptRepositoryInterface {
            public function saveAttempt(Attempt $attempt): void {}
            public function findByIdForUser(string $attemptId, string $userId): ?Attempt { return null; }
            public function nextNumber(string $userId, string $notebookId, string $questionId): int { return 1; }
            public function complete(string $attemptId, string $finalAnswerId, \DateTimeImmutable $completedAt): bool { return true; }
            public function appendAnswer(Answer $answer): void {}
            public function listAnswers(string $attemptId): array { return []; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $this->expectException(\DomainException::class);
        (new AppendAnswerService($repository, $transactions))
            ->append(new AppendAnswerRequestDto('other-user', 'attempt', 'option', 4));
    }
}
