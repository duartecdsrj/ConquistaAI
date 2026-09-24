<?php
declare(strict_types=1);

namespace App\Application\Catalog\Mapper;

use App\Application\Catalog\DTO\Response\SyllabusResponseDto;
use App\Domain\Catalog\Entity\Syllabus;

final class SyllabusResponseMapper
{
    public function map(Syllabus $syllabus): SyllabusResponseDto
    {
        return new SyllabusResponseDto(
            $syllabus->id,
            $syllabus->positionId,
            $syllabus->name,
            $syllabus->publishedAt,
            $syllabus->sourceUrl,
            $syllabus->documentSha256,
            $syllabus->documentOriginalName,
            $syllabus->documentMimeType,
            $syllabus->documentSize,
        );
    }
}
