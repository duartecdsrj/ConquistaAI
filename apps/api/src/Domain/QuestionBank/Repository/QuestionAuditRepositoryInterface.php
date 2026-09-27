<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Repository;

use App\Domain\QuestionBank\ReadModel\QuestionAuditReport;
use App\Domain\QuestionBank\ReadModel\QuestionAuditFindingPage;

interface QuestionAuditRepositoryInterface
{
    public function start(string $algorithmVersion, string $scope): string;
    public function findCompletedRunId(string $algorithmVersion, string $scope): ?string;
    public function completedRunFinishedAt(string $algorithmVersion, string $scope): ?\DateTimeImmutable;

    /** @param array{code:string,confidence:string,message:string} $finding @param array<string,mixed> $before */
    public function record(string $runId, string $questionId, ?string $sourcePdfJobId, ?int $sourcePage, array $finding, array $before, array $after = []): void;

    /** @param array<string,int> $summary */
    public function complete(string $runId, array $summary): void;
    public function fail(string $runId, string $message): void;
    public function latest(): ?QuestionAuditReport;
    public function latestFindings(int $page, int $perPage): ?QuestionAuditFindingPage;
}
