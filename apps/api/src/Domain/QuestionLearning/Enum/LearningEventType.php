<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Enum;
enum LearningEventType: string { case ANSWER_COMPLETED = 'ANSWER_COMPLETED'; case NOT_MASTERED_ENABLED = 'NOT_MASTERED_ENABLED'; case NOT_MASTERED_DISABLED = 'NOT_MASTERED_DISABLED'; }
