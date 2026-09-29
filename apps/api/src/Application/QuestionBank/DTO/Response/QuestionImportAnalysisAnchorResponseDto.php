<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
final readonly class QuestionImportAnalysisAnchorResponseDto { public function __construct(public string $target, public ?int $optionPosition, public int $sourcePage, public ?int $sourceAssetIndex, public string $status) {} }
