<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Entity;

use App\Domain\QuestionBank\Enum\QuestionImportAnswerKeyAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportImageAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportStructureType;
use App\Domain\QuestionBank\ValueObject\QuestionImportFinding;
use App\Domain\QuestionBank\ValueObject\QuestionImportImageAnchor;
use App\Domain\QuestionBank\ValueObject\QuestionImportMetadata;
use App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage;

final readonly class QuestionImportAnalysis
{
    /** @param list<int> $evidencePages @param list<QuestionImportFinding> $findings @param list<QuestionImportImageAnchor> $imageAnchors */
    public function __construct(
        public string $id,
        public string $jobId,
        public ?string $questionId,
        public string $candidateFingerprint,
        public string $schemaVersion,
        public string $algorithmVersion,
        public string $provider,
        public ?string $model,
        public array $evidencePages,
        public QuestionImportMetadata $metadata,
        public QuestionImportStructureType $structureType,
        public QuestionImportAnswerKeyAssessment $answerKeyAssessment,
        public QuestionImportImageAssessment $imageAssessment,
        public QuestionImportTokenUsage $tokenUsage,
        public int $durationMilliseconds,
        public array $findings,
        public array $imageAnchors,
        public \DateTimeImmutable $createdAt,
    ) {
        if (preg_match('/^[a-f0-9]{64}$/', $candidateFingerprint) !== 1 || trim($schemaVersion) === '' || trim($algorithmVersion) === '' || trim($provider) === '' || $durationMilliseconds < 0) {
            throw new \InvalidArgumentException('Análise de importação inválida.');
        }
        foreach ($evidencePages as $page) if (!is_int($page) || $page < 1) throw new \InvalidArgumentException('Páginas de evidência inválidas.');
    }
}
