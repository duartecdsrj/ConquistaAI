<?php
declare(strict_types=1);

namespace App\Application\Performance\Mapper;

use App\Application\Performance\DTO\Response\StudyMapScheduleResponseDto;
use App\Domain\Performance\Entity\StudyMapScheduleItem;

final class StudyMapScheduleResponseMapper
{
    public function map(StudyMapScheduleItem $item): StudyMapScheduleResponseDto
    {
        return new StudyMapScheduleResponseDto($item->taxonomySubjectId, $item->startDate->format('Y-m-d'), $item->endDate->format('Y-m-d'), $item->status->value, $item->completedAt?->format(DATE_ATOM), $item->predecessorSubjectIds, $item->estimatedMinutes);
    }
}
