<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Enum;

enum QuestionImportFindingSeverity: string
{
    case INFO = 'INFO';
    case WARNING = 'WARNING';
    case CRITICAL = 'CRITICAL';
}
