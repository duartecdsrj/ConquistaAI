<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Response;

use App\Domain\QuestionBank\Enum\QuestionImportImageAnchorStatus;
use App\Domain\QuestionBank\Enum\QuestionImportImageTarget;

final readonly class QuestionImportAnalysisImageAnchorDto
{
    public function __construct(public QuestionImportImageTarget $target, public ?string $optionLabel, public int $sourcePage, public ?int $sourceAssetIndex, public QuestionImportImageAnchorStatus $status) {}
}
