<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Provider;

use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzerResponseDto;
use App\Application\QuestionBank\Port\QuestionImportAnalyzerInterface;

final readonly class CallableQuestionImportAnalyzer implements QuestionImportAnalyzerInterface
{
    /** @param \Closure(AnalyzeQuestionImportCandidateRequestDto):QuestionImportAnalyzerResponseDto $callback */
    public function __construct(private \Closure $callback) {}

    public function analyze(AnalyzeQuestionImportCandidateRequestDto $candidate): QuestionImportAnalyzerResponseDto
    {
        return ($this->callback)($candidate);
    }
}
