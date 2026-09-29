<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\ReadModel;

final readonly class PublishedQuestionFilter
{
    public function __construct(
        public int $offset,
        public int $limit,
        public ?string $subjectId,
        public ?string $board,
        public ?int $year,
        public ?string $difficulty,
        public ?string $content = null,
        public ?string $syllabusId = null,
        /** @var list<string> */ public array $taxonomySubjectIds = [],
        public ?string $examId = null,
        public ?string $interactionUserId = null,
        public ?bool $favorite = null,
        public ?bool $reviewLater = null,
        public ?bool $notMastered = null,
    ) {
        if (($favorite !== null || $reviewLater !== null || $notMastered !== null) && $interactionUserId === null) {
            throw new \InvalidArgumentException('Filtros pessoais exigem usuário autenticado.');
        }
    }
}
