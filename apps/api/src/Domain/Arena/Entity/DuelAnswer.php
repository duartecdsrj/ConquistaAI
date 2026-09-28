<?php
declare(strict_types=1);

namespace App\Domain\Arena\Entity;

final readonly class DuelAnswer
{
    public function __construct(
        public string $id,
        public string $duelId,
        public int $position,
        public string $userId,
        public string $optionId,
        public \DateTimeImmutable $receivedAt,
        public int $elapsedMilliseconds,
        public ?bool $isCorrect = null,
        public ?int $correctRank = null,
        public int $points = 0,
    ) {}
}
