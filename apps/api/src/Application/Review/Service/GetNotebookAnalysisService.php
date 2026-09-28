<?php
declare(strict_types=1);

namespace App\Application\Review\Service;

use App\Application\Review\DTO\Response\NotebookAnalysisResponseDto;
use App\Application\Review\Mapper\NotebookAnalysisResponseMapper;
use App\Domain\Review\Repository\NotebookAnalysisActionRepositoryInterface;
use App\Domain\Review\Repository\NotebookAnalysisExecutionRepositoryInterface;

final readonly class GetNotebookAnalysisService
{
    public function __construct(private NotebookAnalysisExecutionRepositoryInterface $executions, private NotebookAnalysisActionRepositoryInterface $actions, private NotebookAnalysisResponseMapper $mapper) {}

    public function execute(string $notebookId, string $userId): ?NotebookAnalysisResponseDto
    {
        $execution = $this->executions->findForNotebookUser($notebookId, $userId);
        return $execution === null ? null : $this->mapper->map($execution, $this->actions->listForExecution($execution->id));
    }
}
