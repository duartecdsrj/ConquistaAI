<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Request;

final readonly class AnalyzeQuestionImportCandidateRequestDto
{
    /** @param list<QuestionImportEvidencePageDto> $evidencePages @param list<QuestionImportImageManifestItemDto> $images @param list<string> $taxonomyPaths */
    public function __construct(
        public string $candidateFingerprint,
        public array $evidencePages,
        public array $images,
        public array $taxonomyPaths,
        public string $schemaVersion = 'question-import-analysis-v1',
    ) {
        if (preg_match('/^[a-f0-9]{64}$/', $candidateFingerprint) !== 1 || $evidencePages === [] || trim($schemaVersion) === '') {
            throw new \InvalidArgumentException('Candidato de importação inválido.');
        }
        foreach ($evidencePages as $page) if (!$page instanceof QuestionImportEvidencePageDto) throw new \InvalidArgumentException('Página de evidência inválida.');
        foreach ($images as $image) if (!$image instanceof QuestionImportImageManifestItemDto) throw new \InvalidArgumentException('Manifesto de imagem inválido.');
        foreach ($taxonomyPaths as $path) if (!is_string($path) || trim($path) === '') throw new \InvalidArgumentException('Taxonomia permitida inválida.');
    }
}
