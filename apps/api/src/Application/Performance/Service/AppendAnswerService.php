<?php
declare(strict_types=1);

namespace App\Application\Performance\Service;

use App\Application\Performance\DTO\Request\AppendAnswerRequestDto;
use App\Application\Performance\Port\TransactionManagerInterface;
use App\Domain\Performance\Entity\Answer;
use App\Domain\Performance\Repository\AttemptRepositoryInterface;

final class AppendAnswerService
{
    public function __construct(
        private readonly AttemptRepositoryInterface $attempts,
        private readonly TransactionManagerInterface $transactions,
        private readonly \DateTimeZone $utc = new \DateTimeZone('UTC'),
    ) {
    }

    public function append(AppendAnswerRequestDto $request): Answer
    {
        return $this->transactions->transactional(function () use ($request): Answer {
            if ($this->attempts->findByIdForUser($request->attemptId, $request->userId) === null) {
                throw new \DomainException('Tentativa nao encontrada.');
            }

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
