<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Provider;

use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzerResponseDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportBatchAnalyzerResponseDto;
use App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage;
use App\Application\QuestionBank\Port\QuestionImportAnalyzerInterface;

final readonly class UnavailableQuestionImportAnalyzer implements QuestionImportAnalyzerInterface
{
    public function __construct(private string $name = 'unconfigured') {}

    public function analyze(AnalyzeQuestionImportCandidateRequestDto $candidate): QuestionImportAnalyzerResponseDto
    {
        throw new \DomainException('Analisador de importação indisponível: '.$this->name);
    }

    public function analyzeBatch(array $candidates): QuestionImportBatchAnalyzerResponseDto
    {
        throw new \DomainException('Analisador de importação indisponível: '.$this->name);
    }
}
