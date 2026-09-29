<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Enum;

enum QuestionImportAnswerKeyAssessment: string
{
    case CONSISTENT = 'CONSISTENT';
    case CONFLICT = 'CONFLICT';
    case UNKNOWN = 'UNKNOWN';
}
