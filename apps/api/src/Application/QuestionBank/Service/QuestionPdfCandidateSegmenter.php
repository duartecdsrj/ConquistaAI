<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Service;

use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Request\QuestionImportEvidencePageDto;
use App\Application\QuestionBank\DTO\Request\QuestionImportImageManifestItemDto;
use App\Domain\QuestionBank\ValueObject\QuestionStatementFingerprint;

final class QuestionPdfCandidateSegmenter
{
    /**
     * @param list<string> $pages
     * @param array<int,list<string>> $pageAssets keyed by one-based PDF page
     * @param list<string> $taxonomyPaths
     * @return list<AnalyzeQuestionImportCandidateRequestDto>
     */
    public function segment(array $pages, array $pageAssets, array $taxonomyPaths): array
    {
        /** @var array<int,list<string>> $fragments */
        $fragments = [];
        $candidates = [];
        foreach ($pages as $offset => $pageText) {
            $page = $offset + 1;
            foreach ($this->fragments($pageText) as $fragment) {
                if ($fragment['startsQuestion'] && $fragments !== []) {
                    $this->appendCandidate($candidates, $fragments, $pages, $pageAssets, $taxonomyPaths);
                    $fragments = [];
                }
                $fragments[$page] ??= [];
                $fragments[$page][] = $fragment['content'];
            }
        }
        if ($fragments !== []) $this->appendCandidate($candidates, $fragments, $pages, $pageAssets, $taxonomyPaths);
        return $candidates;
    }

    /** @return list<array{content:string,startsQuestion:bool}> */
    private function fragments(string $page): array
    {
        if (trim($page) === '') return [];
        $pattern = '/(?=^\s*(?:quest[ãa]o\s*)?\d{1,3}\s*[.)\-–—])/imu';
        $parts = preg_split($pattern, $page, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if ($parts === []) return [];
        $result = [];
        foreach ($parts as $index => $part) {
            $content = trim($part);
            if ($content === '') continue;
            $result[] = ['content' => $content, 'startsQuestion' => $index > 0 || preg_match('/^\s*(?:quest[ãa]o\s*)?\d{1,3}\s*[.)\-–—]/iu', $content) === 1];
        }
        return $result;
    }

    /**
     * @param list<AnalyzeQuestionImportCandidateRequestDto> $candidates
     * @param array<int,list<string>> $fragments
     * @param list<string> $allPages
     * @param array<int,list<string>> $pageAssets
     * @param list<string> $taxonomyPaths
     */
    private function appendCandidate(array &$candidates, array $fragments, array $allPages, array $pageAssets, array $taxonomyPaths): void
    {
        ksort($fragments);
        $content = trim(implode("\n", array_map(static fn (array $parts): string => implode("\n", $parts), $fragments)));
        if (!$this->hasQuestion($content) || !$this->hasOptions($content)) return;
        $evidence = [];
        foreach ($fragments as $page => $parts) $evidence[$page] = implode("\n", $parts);
        if ($this->hasVisualReference($content)) {
            foreach (array_keys($fragments) as $page) {
                foreach ([$page - 1, $page + 1] as $adjacent) {
                    if ($adjacent >= 1 && isset($allPages[$adjacent - 1])) $evidence[$adjacent] ??= $allPages[$adjacent - 1];
                }
            }
        }
        ksort($evidence);
        $evidencePages = array_map(static fn (int $page, string $text): QuestionImportEvidencePageDto => new QuestionImportEvidencePageDto($page, $text), array_keys($evidence), array_values($evidence));
        $images = [];
        foreach (array_keys($evidence) as $page) foreach ($pageAssets[$page] ?? [] as $index => $path) $images[] = new QuestionImportImageManifestItemDto($page, $index, $path);
        $candidates[] = new AnalyzeQuestionImportCandidateRequestDto(QuestionStatementFingerprint::of($content), $evidencePages, $images, $taxonomyPaths);
    }

    private function hasQuestion(string $content): bool
    {
        return preg_match('/(?:^|\n)\s*(?:quest[ãa]o\s*)?\d{1,3}\s*[.)\-–—]/iu', $content) === 1;
    }

    private function hasOptions(string $content): bool
    {
        return preg_match_all('/(?:^|\n)\s*(?:\(?[A-Ea-e]\)|[A-Ea-e][.)])/u', $content) >= 4;
    }

    private function hasVisualReference(string $content): bool
    {
        return preg_match('/\b(?:figura|imagem|gr[aá]fico|tabela|quadro|diagrama|esquema|ilustra[cç][aã]o|mapa|fluxograma)\b/iu', $content) === 1;
    }
}
