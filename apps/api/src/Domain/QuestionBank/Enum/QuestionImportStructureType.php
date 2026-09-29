<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Enum;

enum QuestionImportStructureType: string
{
    case MULTIPLE_CHOICE = 'MULTIPLE_CHOICE';
    case MATCHING = 'MATCHING';
    case ASSERTIONS = 'ASSERTIONS';
}
