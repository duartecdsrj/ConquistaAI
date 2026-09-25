<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ReadModel;

final readonly class PublishedQuestionOption
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
