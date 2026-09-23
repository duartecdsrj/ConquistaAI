<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\Mapper;
use App\Application\Taxonomy\DTO\Response\TaxonomySubjectAliasResponseDto;use App\Domain\Taxonomy\Entity\TaxonomySubjectAlias;
final class TaxonomySubjectAliasResponseMapper { public function toResponse(TaxonomySubjectAlias $alias):TaxonomySubjectAliasResponseDto{return new TaxonomySubjectAliasResponseDto($alias->id,$alias->subjectId,$alias->alias);} }
