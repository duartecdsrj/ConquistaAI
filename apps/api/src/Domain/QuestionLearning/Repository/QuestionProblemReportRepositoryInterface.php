<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Repository;
use App\Domain\QuestionLearning\Entity\QuestionProblemReport;
use App\Domain\QuestionLearning\Enum\ProblemReportStatus;
interface QuestionProblemReportRepositoryInterface { public function save(QuestionProblemReport $report): void; public function appendStatusEvent(string $reportId, string $actorUserId, ?ProblemReportStatus $previous, ProblemReportStatus $next, \DateTimeImmutable $occurredAt): void; public function countOpenForUserQuestion(string $userId, string $questionId): int; }
