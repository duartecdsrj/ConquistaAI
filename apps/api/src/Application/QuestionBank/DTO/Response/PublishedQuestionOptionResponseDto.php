<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Response;

final readonly class PublishedQuestionOptionResponseDto
{
    public function __construct(
        public string $id,
        public string $label,
        public string $content,
        public int $position,
        /** @var list<string> */ public array $assetUrls = [],
    ) {
    }
}
