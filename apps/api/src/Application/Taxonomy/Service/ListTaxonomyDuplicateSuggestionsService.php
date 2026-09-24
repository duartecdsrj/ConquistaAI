<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\Service;
use App\Application\Taxonomy\DTO\Response\TaxonomyDuplicateSuggestionResponseDto;use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
final class ListTaxonomyDuplicateSuggestionsService { public function __construct(private readonly TaxonomySubjectRepositoryInterface $subjects,private readonly SubjectTaxonomyService $taxonomy){} /** @return list<TaxonomyDuplicateSuggestionResponseDto> */ public function list():array{$items=$this->subjects->list(0,1000);$result=[];foreach($items as $index=>$source){foreach(array_slice($items,$index+1) as $candidate){$score=$this->taxonomy->similarity($source->name,$candidate->name);if($score>=0.72)$result[]=new TaxonomyDuplicateSuggestionResponseDto($source->id,$source->name,$candidate->id,$candidate->name,round($score,3));}}usort($result,static fn($a,$b)=>$b->similarity<=>$a->similarity);return $result;} }
