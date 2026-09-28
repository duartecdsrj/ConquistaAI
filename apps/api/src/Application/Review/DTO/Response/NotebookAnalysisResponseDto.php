<?php
declare(strict_types=1);

namespace App\Application\Review\DTO\Response;

final readonly class NotebookAnalysisResponseDto
{
    /** @param list<NotebookAnalysisActionResponseDto> $actions */
    public function __construct(public string $id, public string $notebookId, public string $status, public ?array $summary, public ?string $errorCode, public ?string $completedAt, public array $actions) {}
}
