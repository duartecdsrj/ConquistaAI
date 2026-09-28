<?php
declare(strict_types=1);

namespace App\Domain\Review\ValueObject;

use App\Domain\Review\Entity\NotebookAnalysisExecution;

final readonly class NotebookAnalysisJob
{
    public function __construct(public NotebookAnalysisExecution $execution, public string $userId) {}
}
