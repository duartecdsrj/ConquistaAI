<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ValueObject;

/**
 * Canonical identity for duplicate detection. It removes only PDF extraction
 * layout artefacts; it is not used to rewrite the source question.
 */
final class QuestionStatementFingerprint
{
    public static function of(string $statement): string
    {
        $statement = strtr($statement, ['ﬁ' => 'fi', 'ﬂ' => 'fl', 'ﬀ' => 'ff', 'ﬃ' => 'ffi', 'ﬄ' => 'ffl']);
        if (class_exists(\Normalizer::class)) {
            $statement = \Normalizer::normalize($statement, \Normalizer::FORM_KD) ?: $statement;
        }
        $statement = (string) preg_replace('/\p{Mn}+/u', '', $statement);
        $statement = (string) preg_replace('/[^\p{L}\p{N}]+/u', ' ', $statement);
        return hash('sha256', mb_strtolower(trim($statement)));
    }
}
