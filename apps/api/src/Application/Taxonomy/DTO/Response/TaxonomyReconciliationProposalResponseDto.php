<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\DTO\Response;
final readonly class TaxonomyReconciliationProposalResponseDto { public function __construct(public string $sourceSubjectId,public string $targetSubjectId,public float $confidence,public string $reason){} }
