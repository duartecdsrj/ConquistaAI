<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Response;

use App\Domain\QuestionBank\Enum\QuestionImportAnswerKeyAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportImageAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportStructureType;
use App\Domain\QuestionBank\ValueObject\QuestionImportMetadata;

final readonly class QuestionImportAnalysisOutputDto
{
    /** @param list<QuestionImportAnalyzedOptionDto> $options @param list<int> $evidencePages @param list<string> $taxonomyPath @param list<QuestionImportAnalysisFindingDto> $findings @param list<QuestionImportAnalysisImageAnchorDto> $imageAnchors */
    public function __construct(
        public string $schemaVersion,
        public string $statement,
        public array $options,
        public QuestionImportStructureType $structureType,
        public QuestionImportMetadata $metadata,
        public array $evidencePages,
        public array $taxonomyPath,
        public ?string $parentSubject,
        public string $difficulty,
        public ?string $correctOptionLabel,
        public QuestionImportAnswerKeyAssessment $answerKeyAssessment,
        public QuestionImportImageAssessment $imageAssessment,
        public array $findings,
        public array $imageAnchors,
    ) {}
}
