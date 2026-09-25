<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Application\QuestionBank\DTO\Request\ListQuestionPdfImportJobsRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionPdfImportJobPageResponseDto;
use App\Application\QuestionBank\Mapper\QuestionPdfImportJobResponseMapper;
use App\Domain\QuestionBank\Repository\QuestionPdfImportJobRepositoryInterface;
final class ListQuestionPdfImportJobsService
{
    public function __construct(private readonly QuestionPdfImportJobRepositoryInterface $jobs, private readonly QuestionPdfImportJobResponseMapper $mapper) {}
    public function list(string $userId, ListQuestionPdfImportJobsRequestDto $request): QuestionPdfImportJobPageResponseDto
    {
        $offset = ($request->page - 1) * $request->perPage;
        $items = array_map($this->mapper->map(...), $this->jobs->listForUser($userId, $offset, $request->perPage));
        return new QuestionPdfImportJobPageResponseDto($items, $request->page, $request->perPage, $this->jobs->countForUser($userId));
    }
}
