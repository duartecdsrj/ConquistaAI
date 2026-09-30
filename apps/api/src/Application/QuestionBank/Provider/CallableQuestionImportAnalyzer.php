<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Provider;

use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzerResponseDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportBatchAnalyzerResponseDto;
use App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage;
use App\Application\QuestionBank\Port\QuestionImportAnalyzerInterface;

final readonly class CallableQuestionImportAnalyzer implements QuestionImportAnalyzerInterface
{
    /** @param \Closure(AnalyzeQuestionImportCandidateRequestDto):QuestionImportAnalyzerResponseDto $callback */
    public function __construct(private \Closure $callback) {}

    public function analyze(AnalyzeQuestionImportCandidateRequestDto $candidate): QuestionImportAnalyzerResponseDto
    {
        return ($this->callback)($candidate);
    }

    public function analyzeBatch(array $candidates): QuestionImportBatchAnalyzerResponseDto
    {
        $responses = []; $started = microtime(true); foreach ($candidates as $candidate) $responses[$candidate->candidateFingerprint] = $this->analyze($candidate);
        return new QuestionImportBatchAnalyzerResponseDto($responses, 'callable', null, QuestionImportTokenUsage::unavailable(), (int) ((microtime(true) - $started) * 1000));
    }
}
