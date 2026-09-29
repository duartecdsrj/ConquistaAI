<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Response;

use App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage;

final readonly class QuestionImportAnalyzerResponseDto
{
    public function __construct(
        public QuestionImportAnalysisOutputDto $analysis,
        public string $provider,
        public ?string $model,
        public QuestionImportTokenUsage $tokenUsage,
        public int $durationMilliseconds,
    ) {
        if (trim($provider) === '' || $durationMilliseconds < 0) throw new \InvalidArgumentException('Resposta do analisador inválida.');
    }
}
