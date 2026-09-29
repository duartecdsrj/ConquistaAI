<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ValueObject;

use App\Domain\QuestionBank\Enum\QuestionImportUsageAvailability;

final readonly class QuestionImportTokenUsage
{
    public function __construct(
        public QuestionImportUsageAvailability $availability,
        public ?int $inputTokens,
        public ?int $outputTokens,
        public ?int $totalTokens,
        public ?string $costUsd,
    ) {
        foreach ([$inputTokens, $outputTokens, $totalTokens] as $value) {
            if ($value !== null && $value < 0) throw new \InvalidArgumentException('Tokens não podem ser negativos.');
        }
        if ($availability === QuestionImportUsageAvailability::AVAILABLE && ($inputTokens === null || $outputTokens === null || $totalTokens === null)) {
            throw new \InvalidArgumentException('Uso disponível exige contagens completas.');
        }
        if ($totalTokens !== null && $inputTokens !== null && $outputTokens !== null && $totalTokens < $inputTokens + $outputTokens) {
            throw new \InvalidArgumentException('Total de tokens inválido.');
        }
    }

    public static function unavailable(): self
    {
        return new self(QuestionImportUsageAvailability::UNAVAILABLE, null, null, null, null);
    }
}
