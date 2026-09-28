<?php
declare(strict_types=1);

namespace App\Application\Performance\Service;

use App\Application\Performance\DTO\Request\AppendAnswerRequestDto;
use App\Application\Performance\Port\TransactionManagerInterface;
use App\Domain\Performance\Entity\Answer;
use App\Domain\Performance\Repository\AttemptRepositoryInterface;use App\Domain\Study\Repository\NotebookRepositoryInterface;use App\Domain\Study\Enum\NotebookStatus;

final class AppendAnswerService
{
    public function __construct(
        private readonly AttemptRepositoryInterface $attempts,
        private readonly NotebookRepositoryInterface $notebooks,
        private readonly TransactionManagerInterface $transactions,
        private readonly \DateTimeZone $utc = new \DateTimeZone('UTC'),
    ) {
    }

    public function append(AppendAnswerRequestDto $request): Answer
    {
        return $this->transactions->transactional(function () use ($request): Answer {
            $attempt = $this->attempts->findByIdForUser($request->attemptId, $request->userId);
            if ($attempt === null) {
                throw new \DomainException('Tentativa nao encontrada.');
            }

            $notebook = $this->notebooks->findByIdForUser($attempt->notebookId, $request->userId);
            if ($notebook === null || $notebook->status !== NotebookStatus::IN_PROGRESS) throw new \DomainException('Caderno pausado ou finalizado não aceita respostas.');

            $answers = $this->attempts->listAnswers($request->attemptId);
            $answer = new Answer(
                $this->uuid(),
                $request->attemptId,
                $request->optionId,
                count($answers) + 1,
                $request->elapsedSeconds,
                new \DateTimeImmutable('now', $this->utc),
            );
            $this->attempts->appendAnswer($answer);

            return $answer;
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
