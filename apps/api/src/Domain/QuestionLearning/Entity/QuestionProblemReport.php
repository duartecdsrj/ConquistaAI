<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Entity;
use App\Domain\QuestionLearning\Enum\ProblemReportCategory;
use App\Domain\QuestionLearning\Enum\ProblemReportStatus;
final class QuestionProblemReport { public function __construct(public readonly string $id, public readonly string $questionId, public readonly string $reportedBy, public readonly ProblemReportCategory $category, public readonly string $description, private ProblemReportStatus $status, public readonly \DateTimeImmutable $createdAt, private \DateTimeImmutable $updatedAt) { if (mb_strlen(trim($description)) < 3 || mb_strlen($description) > 2000) { throw new \DomainException('Relato inválido.'); } } public function status(): ProblemReportStatus { return $this->status; } public function changeStatus(ProblemReportStatus $status, \DateTimeImmutable $now): void { $this->status = $status; $this->updatedAt = $now; } public function updatedAt(): \DateTimeImmutable { return $this->updatedAt; } }
