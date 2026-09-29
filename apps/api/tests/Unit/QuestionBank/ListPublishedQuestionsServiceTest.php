<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionBank;

use App\Application\QuestionBank\DTO\Request\ListPublishedQuestionsRequestDto;
use App\Application\QuestionBank\Mapper\PublishedQuestionResponseMapper;
use App\Application\QuestionBank\Service\ListPublishedQuestionsService;
use App\Domain\QuestionBank\ReadModel\PublishedQuestion;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionFilter;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionOption;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionPage;
use App\Domain\QuestionBank\Repository\PublishedQuestionRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class ListPublishedQuestionsServiceTest extends TestCase
{
    public function testMapsOnlyPublicQuestionData(): void
    {
        $repository = new class implements PublishedQuestionRepositoryInterface {
            public ?PublishedQuestionFilter $receivedFilter = null;

            public function findPublished(PublishedQuestionFilter $filter): PublishedQuestionPage
            {
                $this->receivedFilter = $filter;

                return new PublishedQuestionPage([
                    new PublishedQuestion(
                        'question-1',
                        'Enunciado',
                        'MEDIUM',
                        'Banca',
                        2026,
                        [new PublishedQuestionOption('option-1', 'A', 'Alternativa', 1)],
                    ),
                ], 1);
            }
        };

        $result = (new ListPublishedQuestionsService($repository, new PublishedQuestionResponseMapper()))
            ->list(new ListPublishedQuestionsRequestDto(2, 10, 'subject-1', 'Banca', 2026, 'MEDIUM'));

        self::assertSame(1, $result['total']);
        self::assertSame(2, $result['page']);
        self::assertSame(10, $result['perPage']);
        self::assertSame('Enunciado', $result['items'][0]->statement);
        self::assertSame('Alternativa', $result['items'][0]->options[0]->content);
        self::assertSame('subject-1', $repository->receivedFilter?->subjectId);
        self::assertSame(10, $repository->receivedFilter?->offset);
    }
    public function testForwardsCombinedPersonalFiltersOnlyForAuthenticatedUser(): void
    {
        $repository = new class implements PublishedQuestionRepositoryInterface { public ?PublishedQuestionFilter $filter=null; public function findPublished(PublishedQuestionFilter $filter): PublishedQuestionPage { $this->filter=$filter; return new PublishedQuestionPage([], 0); } };
        $service = new ListPublishedQuestionsService($repository, new PublishedQuestionResponseMapper());
        $service->list(new ListPublishedQuestionsRequestDto(1, 25, null, null, null, null, favorite: true, reviewLater: true, notMastered: false), 'user-1');
        self::assertSame('user-1', $repository->filter?->interactionUserId);
        self::assertTrue($repository->filter?->favorite ?? false);
        self::assertTrue($repository->filter?->reviewLater ?? false);
        self::assertFalse($repository->filter?->notMastered ?? true);
    }

    public function testRejectsPersonalFiltersWithoutAuthenticatedUser(): void
    {
        $repository = new class implements PublishedQuestionRepositoryInterface { public function findPublished(PublishedQuestionFilter $filter): PublishedQuestionPage { return new PublishedQuestionPage([], 0); } };
        $this->expectException(\DomainException::class);
        (new ListPublishedQuestionsService($repository, new PublishedQuestionResponseMapper()))->list(new ListPublishedQuestionsRequestDto(1, 25, null, null, null, null, favorite: true));
    }
}
