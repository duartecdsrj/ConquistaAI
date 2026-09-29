<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Provider;

use App\Application\QuestionBank\Port\QuestionImportAnalyzerInterface;

final readonly class QuestionImportAnalyzerFactory
{
    /** @param array<string,QuestionImportAnalyzerInterface> $providers */
    public function __construct(private array $providers) {}

    public function create(string $name): QuestionImportAnalyzerInterface
    {
        return $this->providers[strtolower(trim($name))] ?? new UnavailableQuestionImportAnalyzer($name);
    }
}
