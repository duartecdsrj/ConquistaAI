<?php
declare(strict_types=1);

namespace App\Domain\Arena\Enum;

enum DuelStatus: string
{
    case WAITING = 'WAITING';
    case QUESTION_OPEN = 'QUESTION_OPEN';
    case QUESTION_RESULT = 'QUESTION_RESULT';
    case FINISHED = 'FINISHED';
}
