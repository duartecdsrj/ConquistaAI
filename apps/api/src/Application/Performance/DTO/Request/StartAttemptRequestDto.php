<?php
declare(strict_types=1);

namespace App\Application\Performance\DTO\Request;

final readonly class StartAttemptRequestDto
{
    public function __construct(
        public string $userId,
        public string $notebookId,
        public string $questionId,
    ) {
    }
}
