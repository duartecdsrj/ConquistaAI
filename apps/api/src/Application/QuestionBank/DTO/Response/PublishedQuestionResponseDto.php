<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\DTO\Response;

final readonly class PublishedQuestionResponseDto
{
    /** @param list<PublishedQuestionOptionResponseDto> $options */
    public function __construct(
        public string $id,
        public string $statement,
        public string $difficulty,
        public ?string $board,
        public ?int $year,
        public array $options,
        /** @var list<string> */ public array $taxonomySubjectIds = [],
        public string $status = 'PUBLISHED',
        public ?string $source = null,
        /** @var list<string> */ public array $assetUrls = [],
    ) {
    }
}
