<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\Mapper;
use App\Application\Taxonomy\DTO\Response\TaxonomySubjectResponseDto;use App\Domain\Taxonomy\Entity\TaxonomySubject;
final class TaxonomySubjectResponseMapper { public function toResponse(TaxonomySubject $subject):TaxonomySubjectResponseDto{return new TaxonomySubjectResponseDto($subject->id,$subject->parentId,$subject->name,$subject->slug,$subject->description,$subject->level,$subject->active);} }
