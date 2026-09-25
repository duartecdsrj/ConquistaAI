<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Response\AssignPositionTaxonomySubjectsResponseDto;
use App\Domain\Catalog\Repository\PositionRepositoryInterface;
use App\Domain\Catalog\Repository\PositionTaxonomyAssignmentRepositoryInterface;

final class ListPositionTaxonomySubjectsService
{
    public function __construct(private readonly PositionRepositoryInterface $positions, private readonly PositionTaxonomyAssignmentRepositoryInterface $assignments) {}

    public function list(string $positionId): AssignPositionTaxonomySubjectsResponseDto
    {
        if (!$this->positions->existsById($positionId)) throw new \DomainException('Cargo não encontrado.');
        return new AssignPositionTaxonomySubjectsResponseDto($positionId, $this->assignments->listTaxonomySubjectIds($positionId));
    }
}
