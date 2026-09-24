<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\DTO\Response;
final readonly class TaxonomyDuplicateSuggestionResponseDto { public function __construct(public string $sourceId,public string $sourceName,public string $candidateId,public string $candidateName,public float $similarity){} }
