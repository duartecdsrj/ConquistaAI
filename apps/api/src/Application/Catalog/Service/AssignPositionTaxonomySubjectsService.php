<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Request\AssignPositionTaxonomySubjectsRequestDto;
use App\Application\Catalog\DTO\Response\AssignPositionTaxonomySubjectsResponseDto;
use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Domain\Catalog\Repository\PositionRepositoryInterface;
use App\Domain\Catalog\Repository\PositionTaxonomyAssignmentRepositoryInterface;
use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;

final class AssignPositionTaxonomySubjectsService
{
    public function __construct(
        private readonly PositionRepositoryInterface $positions,
        private readonly TaxonomySubjectRepositoryInterface $taxonomy,
        private readonly PositionTaxonomyAssignmentRepositoryInterface $assignments,
        private readonly TransactionManagerInterface $transactions,
    ) {}

    public function assign(AssignPositionTaxonomySubjectsRequestDto $input): AssignPositionTaxonomySubjectsResponseDto
    {
        return $this->transactions->transactional(function () use ($input): AssignPositionTaxonomySubjectsResponseDto {
            if (!$this->positions->existsById($input->positionId)) throw new \DomainException('Cargo não encontrado.');
            $ids = array_values(array_unique($input->taxonomySubjectIds));
            foreach ($ids as $id) {
                $subject = $this->taxonomy->findById($id);
                if ($subject === null || !$subject->active) throw new \DomainException('Assunto canônico indisponível.');
            }
            $this->assignments->replaceForPosition($input->positionId, $ids);
            return new AssignPositionTaxonomySubjectsResponseDto($input->positionId, $ids);
        });
    }
}
