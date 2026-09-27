<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
final readonly class QuestionAuditReportResponseDto {
    /** @param array<string,int> $summary */
    public function __construct(public string $id, public string $algorithmVersion, public string $scope, public string $status, public array $summary, public string $createdAt, public ?string $startedAt, public ?string $finishedAt, public ?string $errorMessage) {}
}
