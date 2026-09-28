<?php
declare(strict_types=1);

namespace App\Application\Review\Mapper;

use App\Application\Review\DTO\Response\NotebookAnalysisActionResponseDto;
use App\Application\Review\DTO\Response\NotebookAnalysisResponseDto;
use App\Domain\Review\Entity\NotebookAnalysisAction;
use App\Domain\Review\Entity\NotebookAnalysisExecution;

final class NotebookAnalysisResponseMapper
{
    /** @param list<NotebookAnalysisAction> $actions */
    public function map(NotebookAnalysisExecution $execution, array $actions): NotebookAnalysisResponseDto
    {
        return new NotebookAnalysisResponseDto($execution->id, $execution->notebookId, $execution->status->value, $execution->summary, $execution->errorCode, $execution->completedAt?->format(DATE_ATOM), array_map(static fn (NotebookAnalysisAction $action): NotebookAnalysisActionResponseDto => new NotebookAnalysisActionResponseDto($action->id, $action->type->value, $action->taxonomySubjectId, $action->flashcardId, $action->reason, $action->confidence, $action->appliedAt?->format(DATE_ATOM)), $actions));
    }
}
