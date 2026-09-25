<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Service;
final class ImportedQuestionContentSanitizer
{
    /** @param list<array{content?: mixed}> $options */
    public function statement(string $statement, array $options): string
    {
        $clean = trim((string) (preg_replace('/^\s*(?:Ano|Banca|Concurso|Cargo|N[íi]vel)\s*:[^\n]*\R+/iu', '', $statement) ?? $statement));
        $clean = trim((string) (preg_replace('/^\s*Concurso:\s*.*?\bN[íi]vel:\s*(?:Fundamental|M[eé]dio|Superior)\s*/iu', '', $clean) ?? $clean));
        $clean = trim((string) (preg_replace('/^\s*\d{1,3}\s*[.)]\s+/u', '', $clean) ?? $clean));
        if (count($options) < 3) return $clean;
        if (preg_match('/(?:^|[\s\n])([Aa])\s*[.)]\s+.+?(?:[\s\n])([Bb])\s*[.)]\s+.+?(?:[\s\n])([Cc])\s*[.)]\s+/su', $clean, $matches, PREG_OFFSET_CAPTURE) === 1) {
            return trim(substr($clean, 0, $matches[1][1]));
        }
        return $clean;
    }
}