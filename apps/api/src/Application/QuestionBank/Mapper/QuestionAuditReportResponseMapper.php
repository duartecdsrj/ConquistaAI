<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Mapper;
use App\Application\QuestionBank\DTO\Response\QuestionAuditReportResponseDto;
use App\Domain\QuestionBank\ReadModel\QuestionAuditReport;
final class QuestionAuditReportResponseMapper {
    public function map(QuestionAuditReport $report): QuestionAuditReportResponseDto { return new QuestionAuditReportResponseDto($report->id, $report->algorithmVersion, $report->scope, $report->status, $report->summary, $report->createdAt, $report->startedAt, $report->finishedAt, $report->errorMessage); }
}
