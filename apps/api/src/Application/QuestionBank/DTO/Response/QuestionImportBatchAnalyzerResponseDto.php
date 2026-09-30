<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
use App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage;
final readonly class QuestionImportBatchAnalyzerResponseDto {
    /** @param array<string,QuestionImportAnalyzerResponseDto> $responses @param list<string> $invalidCandidateFingerprints */
    public function __construct(public array $responses, public string $provider, public ?string $model, public QuestionImportTokenUsage $tokenUsage, public int $durationMilliseconds, public array $invalidCandidateFingerprints = []) {}
}
