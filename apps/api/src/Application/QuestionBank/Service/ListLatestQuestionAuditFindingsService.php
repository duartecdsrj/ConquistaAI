<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Application\QuestionBank\DTO\Request\ListQuestionAuditFindingsRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionAuditFindingPageResponseDto;
use App\Application\QuestionBank\Mapper\QuestionAuditFindingResponseMapper;
use App\Domain\QuestionBank\Repository\QuestionAuditRepositoryInterface;
final class ListLatestQuestionAuditFindingsService {
    public function __construct(private readonly QuestionAuditRepositoryInterface $reports, private readonly QuestionAuditFindingResponseMapper $mapper) {}
    public function list(ListQuestionAuditFindingsRequestDto $input): ?QuestionAuditFindingPageResponseDto { $page = $this->reports->latestFindings($input->page, $input->perPage); return $page === null ? null : new QuestionAuditFindingPageResponseDto(array_map($this->mapper->map(...), $page->items), $page->page, $page->perPage, $page->total); }
}
