<?php
declare(strict_types=1);

namespace App\Domain\Performance\Entity;

final readonly class Answer
{
    public function __construct(
        public string $id,
        public string $attemptId,
        public ?string $optionId,
        public int $sequence,
        public int $elapsedSeconds,
        public \DateTimeImmutable $submittedAt,
    ) {
        if ($sequence < 1 || $elapsedSeconds < 0) {
            throw new \DomainException('Resposta invalida.');
        }
    }
}
