<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Enum;

enum QuestionQualityCategory: string
{
    case ANSWER_KEY_REVIEW = 'ANSWER_KEY_REVIEW';
    case VISUAL_REVIEW = 'VISUAL_REVIEW';
    case CONTENT_REVIEW = 'CONTENT_REVIEW';
}
