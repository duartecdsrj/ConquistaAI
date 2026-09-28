<?php
declare(strict_types=1);

namespace App\Application\Review\DTO\Response;

final readonly class NotebookAnalysisActionResponseDto
{
    public function __construct(public string $id, public string $type, public ?string $conceptId, public ?string $flashcardId, public string $reason, public float $confidence, public ?string $appliedAt) {}
}
