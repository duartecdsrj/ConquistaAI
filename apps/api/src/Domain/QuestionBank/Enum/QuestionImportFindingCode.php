<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Enum;

enum QuestionImportFindingCode: string
{
    case ANSWER_KEY_CONFLICT = 'ANSWER_KEY_CONFLICT';
    case IMAGE_DISCREPANCY = 'IMAGE_DISCREPANCY';
    case STRUCTURAL_INCONSISTENCY = 'STRUCTURAL_INCONSISTENCY';
    case METADATA_UNVERIFIED = 'METADATA_UNVERIFIED';
}
