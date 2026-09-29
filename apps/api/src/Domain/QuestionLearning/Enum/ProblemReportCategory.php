<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Enum;
enum ProblemReportCategory: string { case CONTENT = 'CONTENT'; case ANSWER_KEY = 'ANSWER_KEY'; case IMAGE = 'IMAGE'; case DUPLICATE = 'DUPLICATE'; case OTHER = 'OTHER'; }
