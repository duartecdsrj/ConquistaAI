<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ValueObject;

final readonly class QuestionImportMetadata
{
    /** @param array<string,list<int>> $evidencePages */
    public function __construct(
        public ?string $exam,
        public ?string $position,
        public ?string $board,
        public ?int $year,
        public array $evidencePages,
    ) {
        foreach ($evidencePages as $field => $pages) {
            if (!in_array($field, ['exam', 'position', 'board', 'year'], true) || !is_array($pages)) {
                throw new \InvalidArgumentException('Evidência de metadado inválida.');
            }
            foreach ($pages as $page) if (!is_int($page) || $page < 1) throw new InvalidArgumentException('Evidência de metadado inválida.');
        }
        foreach (['exam' => $exam, 'position' => $position, 'board' => $board, 'year' => $year] as $field => $value) {
            if ($value !== null && ($evidencePages[$field] ?? []) === []) throw new \InvalidArgumentException('Metadado sem evidência.');
        }
    }
}
