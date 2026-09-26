<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
final readonly class QuestionPdfAssistanceResponseDto { /** @param list<int> $pages */ public function __construct(public string $content,public ?string $provider,public ?string $model,public array $pages){} }
