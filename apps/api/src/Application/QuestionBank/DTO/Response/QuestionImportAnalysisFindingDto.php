<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Response;

use App\Domain\QuestionBank\Enum\QuestionImportFindingCode;
use App\Domain\QuestionBank\Enum\QuestionImportFindingSeverity;

final readonly class QuestionImportAnalysisFindingDto
{
    /** @param list<int> $evidencePages */
    public function __construct(public QuestionImportFindingCode $code, public QuestionImportFindingSeverity $severity, public ?float $confidence, public string $safeSummary, public array $evidencePages) {}
}
