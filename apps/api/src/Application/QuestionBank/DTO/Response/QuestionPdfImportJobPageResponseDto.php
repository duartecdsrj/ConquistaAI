<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
/** @phpstan-type QuestionPdfImportJobList list<QuestionPdfImportJobResponseDto> */
final readonly class QuestionPdfImportJobPageResponseDto
{
    /** @param QuestionPdfImportJobList $items */
    public function __construct(public array $items, public int $page, public int $perPage, public int $total) {}
}
