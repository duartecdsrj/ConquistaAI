<?php
declare(strict_types=1);
namespace App\Infrastructure\Taxonomy;
use App\Domain\Taxonomy\AI\TaxonomyReconciliationAdvisorInterface;use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
final class DeterministicTaxonomyReconciliationAdvisor implements TaxonomyReconciliationAdvisorInterface { public function __construct(private readonly SubjectTaxonomyService $taxonomy){} public function propose(array $subjects):array{$proposals=[];foreach($subjects as $index=>$source){foreach(array_slice($subjects,$index+1) as $target){$confidence=$this->taxonomy->similarity($source['name'],$target['name']);if($confidence>=0.85)$proposals[]=['source_subject_id'=>$source['id'],'target_subject_id'=>$target['id'],'confidence'=>$confidence,'reason'=>'Similaridade determinística de nomes normalizados.'];}}return $proposals;} }
