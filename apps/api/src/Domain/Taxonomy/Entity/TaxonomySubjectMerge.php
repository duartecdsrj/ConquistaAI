<?php
declare(strict_types=1);
namespace App\Domain\Taxonomy\Entity;
final readonly class TaxonomySubjectMerge { public function __construct(public string $id,public string $sourceSubjectId,public string $targetSubjectId,public string $mergedBy,public string $reason){} }
