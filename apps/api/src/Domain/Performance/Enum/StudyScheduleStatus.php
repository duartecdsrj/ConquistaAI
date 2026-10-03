<?php
declare(strict_types=1);

namespace App\Domain\Performance\Enum;

enum StudyScheduleStatus: string
{
    case PLANNED = 'PLANNED';
    case STUDIED = 'STUDIED';
    case COMPLETED = 'COMPLETED';
}
