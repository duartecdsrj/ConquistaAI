<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ReadModel;

final readonly class PublishedQuestionPage
{
    /** @param list<PublishedQuestion> $items */
    public function __construct(
        public array $items,
        public int $total,
    ) {
    }
}
