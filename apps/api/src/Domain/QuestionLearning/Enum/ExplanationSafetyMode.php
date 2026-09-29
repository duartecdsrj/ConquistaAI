<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Enum;
enum ExplanationSafetyMode: string { case CONCEPTUAL_ONLY = 'CONCEPTUAL_ONLY'; case POST_ANSWER = 'POST_ANSWER'; }
