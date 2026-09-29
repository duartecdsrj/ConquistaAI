<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Enum;
enum ExplanationStatus: string { case PENDING = 'PENDING'; case PROCESSING = 'PROCESSING'; case COMPLETED = 'COMPLETED'; case FAILED = 'FAILED'; }
