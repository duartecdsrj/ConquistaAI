<?php
declare(strict_types=1);
namespace App\Domain\Taxonomy\AI;
interface TaxonomyReconciliationAdvisorInterface { /** @param list<array{id:string,name:string}> $subjects @return list<array{source_subject_id:string,target_subject_id:string,confidence:float,reason:string}> */ public function propose(array $subjects):array; }
