<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Enum;

enum QuestionImportImageAssessment: string
{
    case COHERENT = 'COHERENT';
    case DISCREPANCY = 'DISCREPANCY';
    case NOT_APPLICABLE = 'NOT_APPLICABLE';
}
