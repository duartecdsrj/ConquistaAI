<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Mapper;
use App\Application\QuestionLearning\DTO\Response\QuestionProblemReportResponseDto;use App\Domain\QuestionLearning\Entity\QuestionProblemReport;
final readonly class QuestionProblemReportResponseMapper { public function toResponse(QuestionProblemReport $report): QuestionProblemReportResponseDto { return new QuestionProblemReportResponseDto($report->id, $report->questionId, $report->category->value, $report->description, $report->status()->value, $report->createdAt->format(DATE_ATOM), $report->updatedAt()->format(DATE_ATOM)); } }
