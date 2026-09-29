<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Enum;

enum QuestionImportImageTarget: string
{
    case STATEMENT = 'STATEMENT';
    case OPTION = 'OPTION';
}
