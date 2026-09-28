<?php
declare(strict_types=1);

namespace App\Application\Review\Service;

use App\Domain\Review\Repository\NotebookAnalysisExecutionRepositoryInterface;

final readonly class ProcessNextNotebookAnalysisService
{
    public function __construct(private NotebookAnalysisExecutionRepositoryInterface $executions, private ProcessNotebookAnalysisService $processor) {}

    /** @param callable(array):array $provider @param callable(string,string,array,\DateTimeImmutable):void $apply */
    public function execute(callable $provider, callable $apply, \DateTimeImmutable $now): bool
    {
        $job = $this->executions->claimNextPending($now);
        if ($job === null) return false;
        $this->processor->execute(
            $job->execution->notebookId,
            $job->userId,
            $job->execution->algorithmVersion,
            $provider,
            $now,
            fn (array $actions): mixed => $apply($job->execution->id, $job->userId, $actions, $now),
        );
        return true;
    }
}
