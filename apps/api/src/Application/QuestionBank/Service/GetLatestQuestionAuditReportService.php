<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Application\QuestionBank\DTO\Response\QuestionAuditReportResponseDto;
use App\Application\QuestionBank\Mapper\QuestionAuditReportResponseMapper;
use App\Domain\QuestionBank\Repository\QuestionAuditRepositoryInterface;
final class GetLatestQuestionAuditReportService {
    public function __construct(private readonly QuestionAuditRepositoryInterface $reports, private readonly QuestionAuditReportResponseMapper $mapper) {}
    public function get(): ?QuestionAuditReportResponseDto { $report = $this->reports->latest(); return $report === null ? null : $this->mapper->map($report); }
}
