<?php
declare(strict_types=1);

namespace App\Application\Catalog\Mapper;

use App\Application\Catalog\DTO\Response\ExamResponseDto;
use App\Domain\Catalog\Entity\Exam;

final class ExamResponseMapper
{
    public function map(Exam $exam): ExamResponseDto
    {
        return new ExamResponseDto($exam->id, $exam->name, $exam->organizer, $exam->year);
    }
}
