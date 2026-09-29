<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Response;

final readonly class QuestionImportAnalyzedOptionDto
{
    public function __construct(public string $label, public string $content)
    {
        if (preg_match('/^[A-E]$/', $label) !== 1 || trim($content) === '') throw new \InvalidArgumentException('Alternativa analisada inválida.');
    }
}
