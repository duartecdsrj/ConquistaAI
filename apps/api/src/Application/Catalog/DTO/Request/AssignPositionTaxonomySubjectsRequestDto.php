<?php
declare(strict_types=1);

namespace App\Application\Catalog\DTO\Request;

final readonly class AssignPositionTaxonomySubjectsRequestDto
{
    /** @param list<string> $taxonomySubjectIds */
    public function __construct(public string $positionId, public array $taxonomySubjectIds) {}
}
