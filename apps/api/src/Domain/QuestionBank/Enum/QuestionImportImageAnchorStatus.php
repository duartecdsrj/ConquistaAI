<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Enum;

enum QuestionImportImageAnchorStatus: string
{
    case ANCHORED = 'ANCHORED';
    case DISCREPANCY = 'DISCREPANCY';
    case AMBIGUOUS = 'AMBIGUOUS';
}
