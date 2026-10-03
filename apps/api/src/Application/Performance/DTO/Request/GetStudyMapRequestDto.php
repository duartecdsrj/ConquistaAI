<?php
declare(strict_types=1);

namespace App\Application\Performance\DTO\Request;

final readonly class GetStudyMapRequestDto
{
    public function __construct(
        public string $userId,
        public string $examId,
        public ?\DateTimeImmutable $from,
        public ?\DateTimeImmutable $to,
    ) {}
}
