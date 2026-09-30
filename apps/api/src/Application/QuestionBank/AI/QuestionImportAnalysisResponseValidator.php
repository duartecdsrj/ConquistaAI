<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\AI;

use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalysisFindingDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalysisImageAnchorDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalysisOutputDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzedOptionDto;
use App\Domain\QuestionBank\Enum\QuestionImportAnswerKeyAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportFindingCode;
use App\Domain\QuestionBank\Enum\QuestionImportFindingSeverity;
use App\Domain\QuestionBank\Enum\QuestionImportImageAnchorStatus;
use App\Domain\QuestionBank\Enum\QuestionImportImageAssessment;
use App\Domain\QuestionBank\Enum\QuestionImportImageTarget;
use App\Domain\QuestionBank\Enum\QuestionImportStructureType;
use App\Domain\QuestionBank\ValueObject\QuestionImportMetadata;

final class QuestionImportAnalysisResponseValidator
{
    public function validate(array $payload, AnalyzeQuestionImportCandidateRequestDto $input): QuestionImportAnalysisOutputDto
    {
        $pages = array_map(static fn ($page): int => $page->pageNumber, $input->evidencePages);
        $schemaVersion = $this->string($payload['schema_version'] ?? null);
        if ($schemaVersion !== $input->schemaVersion) throw new \DomainException('Versão de schema inválida.');
        $statement = $this->string($payload['statement'] ?? null);
        $options = $this->options($payload['options'] ?? null);
        $metadata = $this->metadata($payload['metadata'] ?? null, $pages);
        [$statement, $metadata] = QuestionImportHeaderMetadataNormalizer::normalize($statement, $metadata, $pages);
        $evidencePages = $this->pages($payload['evidence_pages'] ?? null, $pages);
        $taxonomyPath = $this->taxonomyPath($payload['taxonomy_path'] ?? null, $input->taxonomyPaths);
        $difficulty = $payload['difficulty'] ?? null;
        if (!is_string($difficulty) || !in_array($difficulty, ['EASY', 'MEDIUM', 'HARD'], true)) throw new \DomainException('Dificuldade inválida.');
        $correct = $payload['correct_option'] ?? null;
        if ($correct !== null && (!is_string($correct) || !in_array($correct, array_map(static fn (QuestionImportAnalyzedOptionDto $option): string => $option->label, $options), true))) throw new \DomainException('Gabarito analisado inválido.');
        $answer = $this->enum(QuestionImportAnswerKeyAssessment::class, $payload['answer_key_assessment'] ?? null);
        $image = $this->enum(QuestionImportImageAssessment::class, $payload['image_assessment'] ?? null);
        $findings = $this->findings($payload['findings'] ?? null, $pages);
        if ($answer === QuestionImportAnswerKeyAssessment::CONFLICT && !$this->hasFinding($findings, QuestionImportFindingCode::ANSWER_KEY_CONFLICT)) throw new \DomainException('Conflito de gabarito sem achado.');
        $anchors = $this->anchors($payload['image_anchors'] ?? null, $pages, $options);
        if ($image === QuestionImportImageAssessment::DISCREPANCY && !$this->hasFinding($findings, QuestionImportFindingCode::IMAGE_DISCREPANCY)) throw new \DomainException('Discrepância visual sem achado.');
        return new QuestionImportAnalysisOutputDto($schemaVersion, $statement, $options, $this->enum(QuestionImportStructureType::class, $payload['structure_type'] ?? null), $metadata, $evidencePages, $taxonomyPath, $this->nullableString($payload['parent_subject'] ?? null), $difficulty, $correct, $answer, $image, $findings, $anchors);
    }

    private function string(mixed $value): string { if (!is_string($value) || trim($value) === '') throw new \DomainException('Texto analisado inválido.'); return trim($value); }
    private function nullableString(mixed $value): ?string { return $value === null ? null : $this->string($value); }
    /** @return list<string> */ private function strings(mixed $value): array { if (!is_array($value) || $value === []) throw new \DomainException('Lista analisada inválida.'); return array_map($this->string(...), $value); }
    /** @param list<string> $allowed */
    private function taxonomyPath(mixed $value, array $allowed): array { $path = $this->strings($value); if (count($path) === 1 && str_contains($path[0], '>')) $path = array_values(array_filter(array_map('trim', explode('>', $path[0])), static fn (string $segment): bool => $segment !== '')); if ($path === [] || !in_array(implode(' > ', $path), $allowed, true)) throw new \DomainException('Taxonomia analisada fora das opções permitidas.'); return $path; }
    /** @return list<int> */ private function pages(mixed $value, array $allowed): array { if (!is_array($value) || $value === []) throw new \DomainException('Páginas analisadas inválidas.'); foreach ($value as $page) if (!is_int($page) || !in_array($page, $allowed, true)) throw new \DomainException('Página fora da evidência.'); return array_values(array_unique($value)); }
    /** @return list<QuestionImportAnalyzedOptionDto> */ private function options(mixed $value): array { if (!is_array($value) || !in_array(count($value), [4, 5], true)) throw new \DomainException('Alternativas analisadas inválidas.'); $options = array_map(fn (mixed $option): QuestionImportAnalyzedOptionDto => is_array($option) ? new QuestionImportAnalyzedOptionDto($this->string($option['label'] ?? null), $this->string($option['content'] ?? null)) : throw new \DomainException('Alternativa analisada inválida.'), $value); $labels = array_map(static fn (QuestionImportAnalyzedOptionDto $option): string => $option->label, $options); if (count(array_unique($labels)) !== count($labels)) throw new \DomainException('Alternativas repetidas.'); return $options; }
    private function metadata(mixed $value, array $allowed): QuestionImportMetadata { if (!is_array($value)) throw new \DomainException('Metadados inválidos.'); $evidence = $value['evidence_pages'] ?? null; if (!is_array($evidence)) throw new \DomainException('Evidência de metadados inválida.'); $exam = $this->nullableString($value['exam'] ?? null); $position = $this->nullableString($value['position'] ?? null); $board = $this->nullableString($value['board'] ?? null); $year = $value['year'] ?? null; if ($year !== null && (!is_int($year) || $year < 1900 || $year > 2100)) throw new \DomainException('Ano inválido.'); foreach (['exam' => $exam, 'position' => $position, 'board' => $board, 'year' => $year] as $field => $fieldValue) { $fieldPages = $evidence[$field] ?? []; if (!is_array($fieldPages)) throw new \DomainException('Evidência de metadados inválida.'); if ($fieldValue !== null) $this->pages($fieldPages, $allowed); elseif ($fieldPages !== []) throw new DomainException('Evidência de metadados sem valor.'); $evidence[$field] = $fieldPages; } return new QuestionImportMetadata($exam, $position, $board, $year, $evidence); }
    /** @return list<QuestionImportAnalysisFindingDto> */ private function findings(mixed $value, array $allowed): array { if (!is_array($value)) throw new \DomainException('Achados inválidos.'); return array_map(function (mixed $finding) use ($allowed): QuestionImportAnalysisFindingDto { if (!is_array($finding)) throw new \DomainException('Achado inválido.'); $confidence = $finding['confidence'] ?? null; if ($confidence !== null && (!is_float($confidence) && !is_int($confidence) || $confidence < 0 || $confidence > 1)) throw new \DomainException('Confiança inválida.'); $summary = $this->string($finding['safe_summary'] ?? null); if (preg_match('/\\b(?:alternativa|gabarito)\\s*[A-E]\\b/iu', $summary) === 1) throw new \DomainException('Achado revela resposta.'); return new QuestionImportAnalysisFindingDto($this->enum(QuestionImportFindingCode::class, $finding['code'] ?? null), $this->enum(QuestionImportFindingSeverity::class, $finding['severity'] ?? null), $confidence === null ? null : (float) $confidence, $summary, $this->pages($finding['evidence_pages'] ?? null, $allowed)); }, $value); }
    /** @return list<QuestionImportAnalysisImageAnchorDto> */ private function anchors(mixed $value, array $allowed, array $options): array { if (!is_array($value)) throw new \DomainException('Âncoras inválidas.'); $labels = array_map(static fn (QuestionImportAnalyzedOptionDto $option): string => $option->label, $options); return array_map(function (mixed $anchor) use ($allowed, $labels): QuestionImportAnalysisImageAnchorDto { if (!is_array($anchor)) throw new \DomainException('Âncora inválida.'); $target = $this->enum(QuestionImportImageTarget::class, $anchor['target'] ?? null); $option = $this->nullableString($anchor['option_label'] ?? null); if (($target === QuestionImportImageTarget::OPTION) !== ($option !== null) || ($option !== null && !in_array($option, $labels, true))) throw new \DomainException('Destino de âncora inválido.'); $page = $anchor['source_page'] ?? null; if (!is_int($page) || !in_array($page, $allowed, true)) throw new \DomainException('Página de âncora inválida.'); $asset = $anchor['source_asset_index'] ?? null; if ($asset !== null && (!is_int($asset) || $asset < 0)) throw new \DomainException('Ativo de âncora inválido.'); return new QuestionImportAnalysisImageAnchorDto($target, $option, $page, $asset, $this->enum(QuestionImportImageAnchorStatus::class, $anchor['status'] ?? null)); }, $value); }
    /** @param list<QuestionImportAnalysisFindingDto> $findings */    private function hasFinding(array $findings, QuestionImportFindingCode $code): bool { foreach ($findings as $finding) if ($finding->code === $code) return true; return false; }
    private function enum(string $class, mixed $value): mixed { if (!is_string($value)) throw new \DomainException('Enum analisado inválido.'); try { return $class::from($value); } catch (\ValueError) { throw new \DomainException('Enum analisado inválido.'); } }
}
