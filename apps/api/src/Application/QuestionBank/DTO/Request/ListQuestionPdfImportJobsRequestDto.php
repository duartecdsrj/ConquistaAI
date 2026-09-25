<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Request;
final readonly class ListQuestionPdfImportJobsRequestDto
{
    public function __construct(public int $page, public int $perPage) {}
}
