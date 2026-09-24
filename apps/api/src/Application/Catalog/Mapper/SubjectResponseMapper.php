<?php
declare(strict_types=1);

namespace App\Application\Catalog\Mapper;

use App\Application\Catalog\DTO\Response\SubjectResponseDto;
use App\Domain\Catalog\Entity\Subject;

final class SubjectResponseMapper
{
    public function map(Subject $subject): SubjectResponseDto
    {
        return new SubjectResponseDto($subject->id, $subject->syllabusId, $subject->parentId, $subject->name, $subject->sortOrder, $subject->sourceExcerpt, $subject->sourcePage, $subject->sourceStartOffset, $subject->sourceEndOffset);
    }
}
