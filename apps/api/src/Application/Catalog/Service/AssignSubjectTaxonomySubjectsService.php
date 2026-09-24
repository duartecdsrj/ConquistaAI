<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Request\AssignSubjectTaxonomySubjectsRequestDto;
use App\Application\Catalog\DTO\Response\AssignSubjectTaxonomySubjectsResponseDto;
use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Domain\Catalog\Repository\SubjectRepositoryInterface;
use App\Domain\Catalog\Repository\SubjectTaxonomyAssignmentRepositoryInterface;
use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;

final class AssignSubjectTaxonomySubjectsService
{
    public function __construct(private readonly SubjectRepositoryInterface $subjects, private readonly TaxonomySubjectRepositoryInterface $taxonomy, private readonly SubjectTaxonomyAssignmentRepositoryInterface $assignments, private readonly TransactionManagerInterface $transactions) {}
    public function assign(AssignSubjectTaxonomySubjectsRequestDto $input): AssignSubjectTaxonomySubjectsResponseDto
    {
        return $this->transactions->transactional(function () use ($input): AssignSubjectTaxonomySubjectsResponseDto {
            if (!$this->subjects->exists($input->subjectId)) throw new \DomainException('Assunto do edital nao encontrado.');
            $ids = array_values(array_unique($input->taxonomySubjectIds));
            foreach ($ids as $id) { $subject = $this->taxonomy->findById($id); if ($subject === null || !$subject->active) throw new \DomainException('Assunto canonico indisponivel.'); }
            $this->assignments->replaceForSubject($input->subjectId, $ids);
            return new AssignSubjectTaxonomySubjectsResponseDto($input->subjectId, $ids);
        });
    }
}
