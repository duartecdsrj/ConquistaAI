<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\AI;

use App\Domain\QuestionBank\ValueObject\QuestionImportMetadata;

final class QuestionImportHeaderMetadataNormalizer
{
    /** @param list<int> $allowedPages @return array{string,QuestionImportMetadata} */
    public static function normalize(string $statement, QuestionImportMetadata $metadata, array $allowedPages): array
    {
        if (preg_match("/^\s*\(\s*([^\/()]{2,80}?)\s*[–—-]\s*(.+?)\s*\)\s*/u", $statement, $match) !== 1) return [$statement, $metadata];
        $parts = array_values(array_filter(array_map("trim", explode("/", $match[2])), static fn (string $part): bool => $part !== ""));
        if (count($parts) < 3 || preg_match("/^(19|20)\d{2}$/", $parts[array_key_last($parts)]) !== 1) return [$statement, $metadata];
        $page = $allowedPages[0] ?? null;
        $evidence = $metadata->evidencePages;
        $fallback = static function (string $field, mixed $value) use (&$evidence, $page): mixed {
            if ($value !== null && ($evidence[$field] ?? []) === [] && $page !== null) $evidence[$field] = [$page];
            return $value;
        };
        $board = $fallback("board", $metadata->board ?? trim($match[1]));
        $exam = $fallback("exam", $metadata->exam ?? $parts[0]);
        $position = $fallback("position", $metadata->position ?? implode("/", array_slice($parts, 1, -1)));
        $year = $fallback("year", $metadata->year ?? (int) $parts[array_key_last($parts)]);
        return [trim(mb_substr($statement, mb_strlen($match[0], "UTF-8"), null, "UTF-8")), new QuestionImportMetadata($exam, $position, $board, $year, $evidence)];
    }
}
