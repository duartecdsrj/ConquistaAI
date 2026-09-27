<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
final readonly class QuestionAuditFindingPageResponseDto {
    /** @param list<QuestionAuditFindingResponseDto> $items */
    public function __construct(public array $items, public int $page, public int $perPage, public int $total) {}
}
