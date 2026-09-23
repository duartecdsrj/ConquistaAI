<?php
declare(strict_types=1);

namespace App\Domain\Performance\Entity;

final readonly class Attempt
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $notebookId,
        public string $questionId,
        public int $number,
        public string $context,
        public \DateTimeImmutable $startedAt,
        public ?\DateTimeImmutable $completedAt = null,
    ) {
        if ($number < 1 || !in_array($context, ['STUDY', 'EXAM', 'REVIEW'], true)) {
            throw new \DomainException('Tentativa invalida.');
        }
    }
}
