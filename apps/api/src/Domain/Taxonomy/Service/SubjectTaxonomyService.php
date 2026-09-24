<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\Service;

use App\Domain\Taxonomy\Repository\TaxonomyHierarchyRepositoryInterface;

final class SubjectTaxonomyService
{
    public function normalize(string $value): string
    {
        $normalized = trim(preg_replace('/\\s+/u', ' ', $value) ?? '');
        if ($normalized === '') {
            throw new \InvalidArgumentException('Nome do assunto obrigatorio.');
        }

        return mb_strtolower($normalized, 'UTF-8');
    }

    public function slug(string $value): string
    {
        $normalized = $this->normalize($value);
        $ascii = strtr($normalized, ['á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c', 'ñ' => 'n']);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $ascii) ?? '';
        $slug = trim($slug, '-');

        if ($slug === '') {
            throw new \InvalidArgumentException('Nome do assunto sem slug valido.');
        }

        return $slug;
    }

    public function similarity(string $left, string $right): float
    {
        $leftNormalized = $this->slug($left);
        $rightNormalized = $this->slug($right);
        if ($leftNormalized === $rightNormalized) { return 1.0; }
        $length = max(strlen($leftNormalized), strlen($rightNormalized));
        return $length === 0 ? 0.0 : 1 - (levenshtein($leftNormalized, $rightNormalized) / $length);
    }

    public function assertMoveDoesNotCreateCycle(string $subjectId, ?string $newParentId, TaxonomyHierarchyRepositoryInterface $hierarchy): void
    {
        if ($newParentId === null) {
            return;
        }
        if ($subjectId === $newParentId || in_array($subjectId, $hierarchy->ancestorIds($newParentId), true)) {
            throw new \DomainException('Um assunto nao pode ser pai de si mesmo ou de um descendente.');
        }
    }
}
