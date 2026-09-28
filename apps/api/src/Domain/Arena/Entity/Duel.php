<?php
declare(strict_types=1);

namespace App\Domain\Arena\Entity;

use App\Domain\Arena\Enum\DuelStatus;
use App\Domain\Arena\Enum\DuelVisibility;

final class Duel
{
    /** @param list<string> $questionIds */
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly string $creatorUserId,
        public readonly int $maxPlayers,
        public readonly int $subjectsPerPlayer,
        public readonly int $questionCount,
        public readonly int $questionSeconds,
        public readonly \DateTimeImmutable $createdAt,
        public DuelStatus $status = DuelStatus::WAITING,
        public int $currentPosition = 0,
        public ?\DateTimeImmutable $openedAt = null,
        public ?\DateTimeImmutable $deadlineAt = null,
        public ?\DateTimeImmutable $finishedAt = null,
        public array $questionIds = [],
        public DuelVisibility $visibility = DuelVisibility::PRIVATE,
        public array $players = [],
        public ?array $question = null,
        public bool $answered = false,
    ) {
        if ($maxPlayers < 2 || $maxPlayers > 8 || $subjectsPerPlayer < 1 || $subjectsPerPlayer > 5
            || !in_array($questionCount, [5, 10, 15, 20], true) || !in_array($questionSeconds, [15, 30, 60], true)) {
            throw new \DomainException('Configuração de duelo inválida.');
        }
    }

    public function openQuestion(\DateTimeImmutable $now): void
    {
        if ($this->status !== DuelStatus::WAITING || $this->questionIds === []) {
            throw new \DomainException('Duelo não está pronto para iniciar.');
        }
        $this->status = DuelStatus::QUESTION_OPEN;
        $this->currentPosition = 1;
        $this->openedAt = $now;
        $this->deadlineAt = $now->modify(sprintf('+%d seconds', $this->questionSeconds));
    }
}
