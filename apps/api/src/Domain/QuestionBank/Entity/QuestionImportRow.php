<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Entity;

final readonly class QuestionImportRow
{
    /** @param array<string, mixed> $payload @param list<array{field:string,code:string,message:string}> $errors */
    public function __construct(
        public int $lineNumber,
        public array $payload,
        public bool $valid,
        public array $errors,
    ) {
    }
}
