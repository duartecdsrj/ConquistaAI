<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionBank;

use App\Application\QuestionBank\Service\ProcessQuestionAuditService;
use App\Domain\QuestionBank\Repository\PublishedQuestionRepositoryInterface;
use App\Domain\QuestionBank\Repository\PublishedQuestionAuditChangeDetectorInterface;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionPage;
use App\Domain\QuestionBank\Repository\QuestionAuditRepositoryInterface;
use App\Domain\QuestionBank\Service\QuestionAuditAnalyzer;
use PHPUnit\Framework\TestCase;

final class ProcessQuestionAuditServiceTest extends TestCase
{
    public function testReusesCompletedAuditWithoutReadingOrWritingQuestions(): void
    {
        $questions = $this->createMock(PublishedQuestionRepositoryInterface::class);
        $questions->expects(self::never())->method('findPublished');

        $audit = $this->createMock(QuestionAuditRepositoryInterface::class);
        $audit->expects(self::once())
            ->method('findCompletedRunId')
            ->with('question-audit-v4', 'PUBLISHED')
            ->willReturn('completed-run-id');
        $audit->expects(self::never())->method('start');

        $service = new ProcessQuestionAuditService($questions, $audit, new QuestionAuditAnalyzer());

        self::assertSame('completed-run-id', $service->run());
    }
    public function testStartsNewAuditWhenPublishedInputChangedAfterCompletion(): void
    {
        $questions = new class implements PublishedQuestionRepositoryInterface, PublishedQuestionAuditChangeDetectorInterface {
            public function hasAuditRelevantChangesSince(\DateTimeImmutable $since): bool { return true; }
            public function findPublished(\App\Domain\QuestionBank\ReadModel\PublishedQuestionFilter $filter): PublishedQuestionPage { return new PublishedQuestionPage([], 0); }
        };
        $audit = $this->createMock(QuestionAuditRepositoryInterface::class);
        $audit->method('findCompletedRunId')->willReturn('completed-run-id');
        $audit->method('completedRunFinishedAt')->willReturn(new \DateTimeImmutable('2026-09-27T12:00:00Z'));
        $audit->expects(self::once())->method('start')->with('question-audit-v4', 'PUBLISHED')->willReturn('new-run-id');
        $audit->expects(self::once())->method('complete')->with('new-run-id', ['questions_analyzed' => 0, 'findings' => 0, 'requires_review' => 0]);

        $service = new ProcessQuestionAuditService($questions, $audit, new QuestionAuditAnalyzer());

        self::assertSame('new-run-id', $service->run());
    }

}
