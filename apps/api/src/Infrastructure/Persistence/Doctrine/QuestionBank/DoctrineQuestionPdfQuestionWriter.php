<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Application\QuestionBank\Port\QuestionPdfQuestionWriterInterface;
use App\Domain\QuestionBank\Repository\QuestionDuplicateDetectorInterface;
use App\Domain\QuestionBank\Repository\QuestionTaxonomyAssignmentRepositoryInterface;
use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use App\Domain\QuestionBank\ValueObject\QuestionStatementFingerprint;
use App\Domain\Taxonomy\Entity\TaxonomySubject;
use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionAssetRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineQuestionPdfQuestionWriter implements QuestionPdfQuestionWriterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly QuestionDuplicateDetectorInterface $duplicates,
        private readonly TaxonomySubjectRepositoryInterface $taxonomy,
        private readonly QuestionTaxonomyAssignmentRepositoryInterface $assignments,
        private readonly SubjectTaxonomyService $slugs,
        private readonly ImportedQuestionContentSanitizer $content,
    ) {}

    public function write(string $createdBy, array $questions, array $pageAssets = [], ?string $sourcePdfJobId = null): array
    {
        $statements = array_values(array_filter(array_map(fn(mixed $question): string => is_array($question) && is_string($question['statement'] ?? null) ? $this->content->statement($question['statement'], is_array($question['options'] ?? null) ? $question['options'] : []) : '', $questions)));
        $existing = $this->duplicates->findExistingByStatements($statements);
        $created = $duplicates = $classified = $failed = $createdSubjects = 0;
        $pendingAnswerKeys = [];

        foreach ($questions as $question) {
            if (!is_array($question) || ($question['type'] ?? null) !== 'MULTIPLE_CHOICE' || !is_string($question['statement'] ?? null)) { $failed++; continue; }
            $options = array_values(array_filter($question['options'] ?? [], static fn(mixed $option): bool => is_array($option) && is_string($option['content'] ?? null) && trim($option['content']) !== ''));
            $statement = $this->content->statement($question['statement'], $options);
            // O catálogo objetivo contém apenas questões objetivas de quatro ou cinco opções.
            if ($statement === '' || !in_array(count($options), [4, 5], true)) { $failed++; continue; }
            $normalizedOptions = array_map(fn (array $option): string => $this->norm($this->content->option($option['content'])), $options);
            if (count(array_unique($normalizedOptions)) !== count($normalizedOptions)) { $failed++; continue; }
            $key = $this->norm($statement);
            if (isset($existing[$key])) { $duplicates++; continue; }
            $placement = $this->specificTaxonomy($question);
            if ($placement === null) { $failed++; continue; }
            [$taxonomy, $wasCreated] = $placement;

            $record = new QuestionRecord();
            $record->id = $this->id();
            $record->syllabusId = null;
            $record->statement = $statement;
            $record->difficulty = in_array($question['difficulty'] ?? null, ['EASY', 'MEDIUM', 'HARD'], true) ? $question['difficulty'] : 'MEDIUM';
            $record->board = is_string($question['board'] ?? null) ? mb_substr(trim($question['board']), 0, 190) ?: null : null;
            $record->examYear = is_int($question['year'] ?? null) && $question['year'] > 1900 && $question['year'] < 2100 ? $question['year'] : null;
            $record->sourcePdfJobId = $sourcePdfJobId;
            $record->sourcePdfPages = $this->sourcePages($question);
            $record->source = is_string($question['exam'] ?? null) ? mb_substr(trim($question['exam']), 0, 255) ?: null : null;
            $record->origin = 'EXAM';
            $record->status = 'REVIEW';
            $record->createdBy = $createdBy;
            $record->createdAt = new \DateTimeImmutable('now');
            $record->updatedAt = $record->createdAt;
            $this->em->persist($record);
            $this->persistQuestionAssets($record, $statement, $question, $pageAssets);
            $optionIds = $this->persistOptions($record, $options, $pageAssets);
            $correctLabel = strtoupper((string) ($question['correct_option'] ?? ''));
            if (isset($optionIds[$correctLabel])) $pendingAnswerKeys[] = [$record, $optionIds[$correctLabel], in_array($question['answer_key_source'] ?? null, ['OFFICIAL', 'AI_ESTIMATED'], true) ? $question['answer_key_source'] : 'OFFICIAL'];
            $this->assignments->replaceForQuestion($record->id, [$taxonomy->id]);
            $existing[$key] = $record->id;
            $created++;
            $classified++;
            $createdSubjects += $wasCreated ? 1 : 0;
        }
        $this->em->flush();
        foreach ($pendingAnswerKeys as [$record, $optionId, $source]) { $record->correctOptionId = $optionId; $record->answerKeySource = $source; }
        if ($pendingAnswerKeys !== []) $this->em->flush();
        return compact('created', 'duplicates', 'classified', 'failed', 'createdSubjects');
    }

    /** @return array{TaxonomySubject,bool}|null */
    private function specificTaxonomy(array $question): ?array
    {
        $path = $this->taxonomyPath($question);
        if ($path === []) return null;
        $name = $path[array_key_last($path)];
        $slug = $this->slugs->slug($name);
        $parentName = is_string($question['parent_subject'] ?? null) ? $question['parent_subject'] : (count($path) > 1 ? $path[count($path) - 2] : null);
        $parent = $this->parentTaxonomy($parentName);
        if ($parent === null) {
            $candidate = $this->taxonomy->findBySlug($slug) ?? $this->taxonomy->findByComparableSlug($slug);
            if ($candidate === null) return null;
            if ($this->taxonomy->hasChildren($candidate->id) || $this->isGenericLeaf($slug)) return null;
            return [$candidate, false];
        }
        if ($this->isGenericLeaf($slug)) return null;

        // A folha é resolvida primeiro sob o pai informado pelo classificador. Isso evita
        // classificar, por exemplo, "Dados Abertos" na raiz quando pertence a TI.
        $subject = $this->taxonomy->findByParentAndSlug($parent->id, $slug);
        if ($subject === null) {
            $candidate = $this->taxonomy->findBySlug($slug) ?? $this->taxonomy->findByComparableSlug($slug);
            $subject = $candidate !== null && $candidate->parentId === $parent->id ? $candidate : null;
        }
        if ($subject !== null) return $this->taxonomy->hasChildren($subject->id) ? null : [$subject, false];

        $subject = new TaxonomySubject($this->id(), $parent->id, $name, $slug, null, $parent->level + 1, true);
        $this->taxonomy->save($subject);
        return [$subject, true];
    }

    /** @return list<string> */
    private function taxonomyPath(array $question): array
    {
        $path = is_array($question['taxonomy_path'] ?? null) ? $question['taxonomy_path'] : [];
        $path = array_values(array_filter(array_map(static fn(mixed $item): string => is_string($item) ? trim($item) : '', $path)));
        if ($path !== []) return $path;
        return is_string($question['subject'] ?? null) && trim($question['subject']) !== '' ? [trim($question['subject'])] : [];
    }

    private function isGenericLeaf(string $slug): bool
    {
        return in_array($slug, ['conhecimentos-gerais', 'conhecimentos-especificos', 'tecnologia-da-informacao', 'informatica', 'direito', 'administracao', 'lingua-portuguesa', 'portugues', 'banco-de-dados'], true);
    }

    private function parentTaxonomy(?string $name): ?TaxonomySubject
    {
        if ($name === null || trim($name) === '') return null;
        $slug = $this->slugs->slug($name);
        return $this->taxonomy->findBySlug($slug) ?? $this->taxonomy->findByComparableSlug($slug);
    }

    private function persistQuestionAssets(QuestionRecord $question, string $statement, array $source, array $pageAssets): void
    {
        if (!$this->hasVisualReference($statement)) return;
        $order = 0;
        foreach ($this->verifiedAssets($source) as $asset) $this->persistAsset($question, null, $asset['page'], $asset['path'], ++$order);
    }

    private function persistOptions(QuestionRecord $question, array $options, array $pageAssets): array
    {
        $optionIds = [];
        $assetOrder = 0;
        foreach ($options as $index => $option) {
            $record = new QuestionOptionRecord();
            $record->id = $this->id();
            $record->questionId = $question->id;
            $record->label = chr(65 + $index);
            $record->content = $this->content->option($option['content']);
            $record->sortOrder = $index + 1;
            $record->createdAt = $question->createdAt;
            $this->em->persist($record);
            $optionIds[$record->label] = $record->id;
            if ($this->hasVisualReference($record->content)) foreach ($this->verifiedAssets($option) as $asset) $this->persistAsset($question, $record->id, $asset['page'], $asset['path'], ++$assetOrder);
        }
        return $optionIds;
    }

    /**
     * Page references are provenance only. An image is persisted solely after a
     * deterministic crop workflow or manual review records its explicit association.
     * @return list<array{path:string,page:int}>
     */
    private function verifiedAssets(array $source): array
    {
        $assets = [];
        foreach ((array) ($source['verified_assets'] ?? []) as $asset) {
            if (!is_array($asset) || !is_string($asset['path'] ?? null) || trim($asset['path']) === '') continue;
            $page = $asset['page'] ?? null;
            if (!is_int($page) && !(is_string($page) && ctype_digit($page))) continue;
            if ((int) $page < 1) continue;
            $assets[] = ['path' => trim($asset['path']), 'page' => (int) $page];
        }
        return $assets;
    }

    private function persistAsset(QuestionRecord $question, ?string $optionId, int $page, string $path, int $order): void
    {
        $asset = new QuestionAssetRecord();
        $asset->id = $this->id();
        $asset->questionId = $question->id;
        $asset->optionId = $optionId;
        $asset->pageNumber = $page;
        $asset->path = $path;
        $asset->mimeType = 'image/png';
        $asset->sortOrder = $order;
        $asset->createdAt = $question->createdAt;
        $this->em->persist($asset);
    }

    /** @return list<int> */
    private function sourcePages(array $source): array { $pages = array_merge((array) ($source['pages'] ?? []), (array) ($source['image_pages'] ?? [])); $pages = array_values(array_unique(array_filter(array_map(static fn(mixed $page): int => is_int($page) ? $page : (is_string($page) && ctype_digit($page) ? (int) $page : 0), $pages), static fn(int $page): bool => $page > 0))); sort($pages); return $pages; }

    private function hasVisualReference(string $content): bool { return preg_match('/\b(?:figura|imagem|gr[aá]fico|tabela|quadro|diagrama|esquema|ilustra[cç][aã]o|mapa|fluxograma)\b/iu', $content) === 1; }
    private function norm(string $content): string { return QuestionStatementFingerprint::of($content); }
    private function id(): string { $bytes = random_bytes(16); $bytes[6] = chr((ord($bytes[6]) & 15) | 64); $bytes[8] = chr((ord($bytes[8]) & 63) | 128); return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4)); }
}
