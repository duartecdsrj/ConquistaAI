<?php
declare(strict_types=1);

namespace App\Application\Performance\Service;

use App\Application\Performance\DTO\Request\StartAttemptRequestDto;
use App\Application\Performance\Port\TransactionManagerInterface;
use App\Domain\Performance\Entity\Attempt;
use App\Domain\Performance\Repository\AttemptRepositoryInterface;
use App\Domain\Study\Repository\NotebookRepositoryInterface;

final class StartAttemptService
{
    public function __construct(
        private readonly NotebookRepositoryInterface $notebooks,
        private readonly AttemptRepositoryInterface $attempts,
        private readonly TransactionManagerInterface $transactions,
        private readonly \DateTimeZone $utc = new \DateTimeZone('UTC'),
    ) {
    }

    public function start(StartAttemptRequestDto $request): Attempt
    {
        return $this->transactions->transactional(function () use ($request): Attempt {
            $notebook = $this->notebooks->findByIdForUser($request->notebookId, $request->userId);
            if ($notebook === null) {
                throw new \DomainException('Caderno nao encontrado.');
            }
            if ($notebook->status === \App\Domain\Study\Enum\NotebookStatus::FINISHED) {
                throw new \DomainException('Caderno finalizado nao aceita novas tentativas.');
            }

            if (!in_array($request->questionId, $notebook->selection->questionIds, true)) {
                throw new \DomainException('A questao nao pertence ao caderno.');
            }

            $attempt = new Attempt(
                $this->uuid(),
                $request->userId,
                $request->notebookId,
                $request->questionId,
                $this->attempts->nextNumber($request->userId, $request->notebookId, $request->questionId),
                $notebook->mode->value,
                new \DateTimeImmutable('now', $this->utc),
            );
            $this->attempts->saveAttempt($attempt);

            return $attempt;
        });
    }

    private function uuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 15) | 64);
        $bytes[8] = chr((ord($bytes[8]) & 63) | 128);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
