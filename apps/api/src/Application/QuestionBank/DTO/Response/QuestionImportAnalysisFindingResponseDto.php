<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
final readonly class QuestionImportAnalysisFindingResponseDto { /** @param list<int> $evidencePages */ public function __construct(public string $code, public string $severity, public ?float $confidence, public string $safeSummary, public array $evidencePages) {} }
