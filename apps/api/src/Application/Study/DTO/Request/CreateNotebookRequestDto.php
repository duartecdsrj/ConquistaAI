<?php
declare(strict_types=1);

namespace App\Application\Study\DTO\Request;

final readonly class CreateNotebookRequestDto
{
    /** @param array{subject_id?: string, subject_ids?: list<string>, syllabus_id?: string, exam_id?: string, position_id?: string, board?: string, year?: int, difficulty?: string} $filters */
    public function __construct(
        public string $userId,
        public string $name,
        public string $mode,
        public int $quantity,
        public array $filters,
    ) {
    }
}
