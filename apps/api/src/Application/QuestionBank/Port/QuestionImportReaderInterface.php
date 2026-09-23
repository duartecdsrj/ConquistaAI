<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Port;

interface QuestionImportReaderInterface
{
    /** @return list<array<string, mixed>> */
    public function read(string $format, string $content): array;
}
