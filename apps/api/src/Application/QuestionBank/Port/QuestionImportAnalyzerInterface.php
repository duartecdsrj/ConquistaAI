<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Port;

use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzerResponseDto;

interface QuestionImportAnalyzerInterface
{
    public function analyze(AnalyzeQuestionImportCandidateRequestDto $candidate): QuestionImportAnalyzerResponseDto;
}
