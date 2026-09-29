<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Enum;
enum ProblemReportStatus: string { case OPEN = 'OPEN'; case TRIAGED = 'TRIAGED'; case RESOLVED = 'RESOLVED'; case REJECTED = 'REJECTED'; }
