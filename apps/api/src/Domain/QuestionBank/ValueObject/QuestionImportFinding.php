<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ValueObject;

use App\Domain\QuestionBank\Enum\QuestionImportFindingCode;
use App\Domain\QuestionBank\Enum\QuestionImportFindingSeverity;

final readonly class QuestionImportFinding
{
    /** @param list<int> $evidencePages */
    public function __construct(
        public string $id,
        public QuestionImportFindingCode $code,
        public QuestionImportFindingSeverity $severity,
        public ?float $confidence,
        public string $safeSummary,
        public array $evidencePages,
    ) {
        if ($confidence !== null && ($confidence < 0 || $confidence > 1)) throw new \InvalidArgumentException('Confiança inválida.');
        if (trim($safeSummary) === '' || mb_strlen($safeSummary) > 500) throw new \InvalidArgumentException('Achado de importação inválido.');
        foreach ($evidencePages as $page) if (!is_int($page) || $page < 1) throw new \InvalidArgumentException('Achado de importação inválido.');
    }
}
