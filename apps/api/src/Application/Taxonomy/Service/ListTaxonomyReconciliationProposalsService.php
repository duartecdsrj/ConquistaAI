<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\Service;
use App\Application\Taxonomy\DTO\Response\TaxonomyReconciliationProposalResponseDto;use App\Domain\Taxonomy\AI\TaxonomyReconciliationAdvisorInterface;use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;
final class ListTaxonomyReconciliationProposalsService { public function __construct(private readonly TaxonomySubjectRepositoryInterface $subjects,private readonly TaxonomyReconciliationAdvisorInterface $advisor){} /** @return list<TaxonomyReconciliationProposalResponseDto> */ public function list():array{$subjects=array_map(static fn($s)=>['id'=>$s->id,'name'=>$s->name],$this->subjects->list(0,1000));return array_map(static fn(array $p)=>new TaxonomyReconciliationProposalResponseDto($p['source_subject_id'],$p['target_subject_id'],$p['confidence'],$p['reason']),$this->advisor->propose($subjects));} }
