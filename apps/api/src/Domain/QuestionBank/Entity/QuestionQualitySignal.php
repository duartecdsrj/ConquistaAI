<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Entity;

use App\Domain\QuestionBank\Enum\QuestionQualityCategory;

final readonly class QuestionQualitySignal
{
    public function __construct(
        public string $questionId,
        public string $analysisId,
        public QuestionQualityCategory $category,
        public string $safeMessage,
        public \DateTimeImmutable $detectedAt,
    ) {
        if (trim($safeMessage) === '' || mb_strlen($safeMessage) > 500) throw new \InvalidArgumentException('Sinal de qualidade inválido.');
    }
}
