<?php
declare(strict_types=1);

namespace App\Infrastructure\QuestionBank;

final class CorrectionEvidenceLocator
{
    /** @param array<string,mixed> $snapshot @return array<string,string> */
    public static function automaticReferences(array $snapshot): array
    {
        $statement = $snapshot["statement"] ?? null;
        if (!is_string($statement)) return [];

        $longest = "";
        foreach (preg_split("/\\R/u", $statement) ?: [] as $line) {
            $line = trim($line);
            $line = preg_replace("/^[^\\p{L}\\p{N}]+|[^\\p{L}\\p{N}]+$/u", "", $line) ?? "";
            if ($line !== "" && mb_strlen($line, "UTF-8") > mb_strlen($longest, "UTF-8")) $longest = $line;
        }

        return $longest === "" ? [] : ["statement_longest_line" => $longest];
    }

    /** @param list<int> $evidencePages @param list<int> $matchedPages @return list<int> */
    public static function mergeEvidenceWindows(array $evidencePages, array $matchedPages): array
    {
        $pages = array_fill_keys(array_map("intval", $evidencePages), true);
        foreach ($matchedPages as $matchedPage) {
            $matchedPage = (int) $matchedPage;
            if ($matchedPage < 1) continue;
            for ($page = max(1, $matchedPage - 2); $page <= $matchedPage + 2; $page++) $pages[$page] = true;
        }
        $result = array_map("intval", array_keys($pages));
        sort($result, SORT_NUMERIC);
        return $result;
    }
}
