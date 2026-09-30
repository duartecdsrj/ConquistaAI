<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ReadModel;

final readonly class PublishedQuestion
{
    /** @param list<PublishedQuestionOption> $options */
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
        public ?string $answerKeySource = null,
        public ?string $sourcePdfJobId = null,
        /** @var list<int> */ public array $sourcePdfPages = [],
        /** @var list<string> */ public array $taxonomySubjectNames = [],
        public ?string $qualityNotice = null,
    ) {
    }
}
