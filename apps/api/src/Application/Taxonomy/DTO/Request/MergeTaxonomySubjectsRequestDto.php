<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\DTO\Request;
final readonly class MergeTaxonomySubjectsRequestDto { public function __construct(public string $sourceSubjectId,public string $targetSubjectId,public string $mergedBy,public string $reason){} }
