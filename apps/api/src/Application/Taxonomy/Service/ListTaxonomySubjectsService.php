<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\Service;
use App\Application\Taxonomy\DTO\Request\ListTaxonomySubjectsRequestDto;use App\Application\Taxonomy\DTO\Response\PaginatedTaxonomySubjectsResponseDto;use App\Application\Taxonomy\Mapper\TaxonomySubjectResponseMapper;use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;
final class ListTaxonomySubjectsService { public function __construct(private readonly TaxonomySubjectRepositoryInterface $subjects,private readonly TaxonomySubjectResponseMapper $mapper){} public function list(ListTaxonomySubjectsRequestDto $input):PaginatedTaxonomySubjectsResponseDto{return new PaginatedTaxonomySubjectsResponseDto(array_map($this->mapper->toResponse(...),$this->subjects->list($input->offset(),$input->perPage)),$input->page,$input->perPage,$this->subjects->count());} }
