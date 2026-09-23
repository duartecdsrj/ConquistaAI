<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\DTO\Response;
final readonly class PaginatedTaxonomySubjectsResponseDto { /** @param list<TaxonomySubjectResponseDto> $items */ public function __construct(public array $items,public int $page,public int $perPage,public int $total){} }
