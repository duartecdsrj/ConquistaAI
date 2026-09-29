<?php
declare(strict_types=1);

namespace App\Infrastructure\QuestionBank;

use App\Application\QuestionBank\Port\QuestionImportAnalyzerInterface;
use App\Application\QuestionBank\Provider\QuestionImportAnalyzerFactory;
use App\Application\QuestionBank\Provider\UnavailableQuestionImportAnalyzer;
use App\Infrastructure\Database;

final class ConfiguredQuestionImportAnalyzerFactory
{
    /** @param array<string,QuestionImportAnalyzerInterface> $providers */
    public static function create(array $providers = []): QuestionImportAnalyzerInterface
    {
        $name = strtolower(Database::env('QUESTION_IMPORT_ANALYZER', 'codex'));
        return (new QuestionImportAnalyzerFactory($providers + ['codex' => new CodexQuestionImportAnalyzer(), 'unavailable' => new UnavailableQuestionImportAnalyzer('unavailable')]))->create($name);
    }
}
