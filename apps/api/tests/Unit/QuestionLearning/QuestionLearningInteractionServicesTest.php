<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionLearning;

use App\Application\QuestionLearning\DTO\Request\CreateQuestionCommentRequestDto;
use App\Application\QuestionLearning\DTO\Request\CreateQuestionProblemReportRequestDto;
use App\Application\QuestionLearning\DTO\Request\RequestQuestionExplanationRequestDto;
use App\Application\QuestionLearning\DTO\Request\SaveQuestionNoteRequestDto;
use App\Application\QuestionLearning\Mapper\QuestionCommentResponseMapper;
use App\Application\QuestionLearning\Mapper\QuestionExplanationResponseMapper;
use App\Application\QuestionLearning\Mapper\QuestionNoteResponseMapper;
use App\Application\QuestionLearning\Mapper\QuestionProblemReportResponseMapper;
use App\Application\QuestionLearning\Service\CreateQuestionCommentService;
use App\Application\QuestionLearning\Service\CreateQuestionProblemReportService;
use App\Application\QuestionLearning\Service\RequestQuestionExplanationService;
use App\Application\QuestionLearning\Service\SaveQuestionNoteService;
use App\Domain\Performance\Entity\Answer;
use App\Domain\Performance\Entity\Attempt;
use App\Domain\Performance\Repository\AttemptRepositoryInterface;
use App\Domain\QuestionBank\ReadModel\PublishedQuestion;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionOption;
use App\Domain\QuestionBank\Repository\FrozenQuestionReaderInterface;
use App\Domain\QuestionLearning\Entity\QuestionComment;
use App\Domain\QuestionLearning\Entity\QuestionExplanationExecution;
use App\Domain\QuestionLearning\Entity\QuestionNote;
use App\Domain\QuestionLearning\Entity\QuestionProblemReport;
use App\Domain\QuestionLearning\Enum\ProblemReportCategory;
use App\Domain\QuestionLearning\Enum\ProblemReportStatus;
use App\Domain\QuestionLearning\Repository\QuestionCommentRepositoryInterface;
use App\Domain\QuestionLearning\Repository\QuestionExplanationExecutionRepositoryInterface;
use App\Domain\QuestionLearning\Repository\QuestionNoteRepositoryInterface;
use App\Domain\QuestionLearning\Repository\QuestionProblemReportRepositoryInterface;
use App\Domain\QuestionLearning\Service\QuestionExplanationSafetyPolicy;
use App\Domain\QuestionLearning\ValueObject\QuestionExplanationJob;
use PHPUnit\Framework\TestCase;

final class QuestionLearningInteractionServicesTest extends TestCase
{
    public function testNotesAreUpdatedOnlyForTheirOwner(): void
    {
        $notes = new class implements QuestionNoteRepositoryInterface { public array $items=[]; public function findForUserQuestion(string $userId,string $questionId):?QuestionNote{return $this->items[$userId.':'.$questionId]??null;} public function save(QuestionNote $note):void{$this->items[$note->userId.':'.$note->questionId]=$note;} public function deleteForUserQuestion(string $userId,string $questionId):void{unset($this->items[$userId.':'.$questionId]);} };
        $service = new SaveQuestionNoteService($this->questions(), $notes, new QuestionNoteResponseMapper());
        $now = new \DateTimeImmutable('2026-01-01T00:00:00Z');
        $service->execute('user-a', new SaveQuestionNoteRequestDto('question-1', 'primeira'), $now);
        $other = $service->execute('user-b', new SaveQuestionNoteRequestDto('question-1', 'privada'), $now);
        $response = $service->execute('user-a', new SaveQuestionNoteRequestDto('question-1', 'atualizada'), $now->modify('+1 minute'));
        self::assertSame('atualizada', $response->content);
        self::assertSame('privada', $other->content);
    }

    public function testCommentCannotReferenceParentFromAnotherQuestion(): void
    {
        $parent = new QuestionComment('comment-a', 'question-other', 'user-a', null, 'pai', new \DateTimeImmutable(), new \DateTimeImmutable());
        $comments = new class($parent) implements QuestionCommentRepositoryInterface { public function __construct(private QuestionComment $parent) {} public function listForQuestion(string $questionId,int $page,int $perPage):array{return [];} public function countForQuestion(string $questionId):int{return 0;} public function findForQuestion(string $commentId,string $questionId):?QuestionComment{return $this->parent->id===$commentId&&$this->parent->questionId===$questionId?$this->parent:null;} public function save(QuestionComment $comment):void{} };
        $service = new CreateQuestionCommentService($this->questions(), $comments, new QuestionCommentResponseMapper());
        $this->expectException(\DomainException::class);
        $service->execute('user-a', new CreateQuestionCommentRequestDto('question-1', 'resposta', 'comment-a'), new \DateTimeImmutable());
    }

    public function testReportPersistsInitialAuditEvent(): void
    {
        $reports = new class implements QuestionProblemReportRepositoryInterface { public ?QuestionProblemReport $report=null; public array $events=[]; public function save(QuestionProblemReport $report):void{$this->report=$report;} public function appendStatusEvent(string $reportId,string $actorUserId,?ProblemReportStatus $previous,ProblemReportStatus $next,\DateTimeImmutable $occurredAt):void{$this->events[]=[$reportId,$actorUserId,$previous,$next];} public function countOpenForUserQuestion(string $userId,string $questionId):int{return 0;} };
        $service = new CreateQuestionProblemReportService($this->questions(), $reports, new QuestionProblemReportResponseMapper());
        $response = $service->execute('user-a', new CreateQuestionProblemReportRequestDto('question-1', ProblemReportCategory::CONTENT, 'Enunciado truncado'), new \DateTimeImmutable());
        self::assertSame('OPEN', $response->status);
        self::assertSame(ProblemReportStatus::OPEN, $reports->events[0][3]);
    }

    public function testExplanationRequestIsIdempotentAndScopedToUser(): void
    {
        $executions = new class implements QuestionExplanationExecutionRepositoryInterface { public array $items=[]; public int $saved=0; private function key(string $u,string $q,?string $a,string $v):string{return implode(':',[$u,$q,$a??'none',$v]);} public function findById(string $id):?QuestionExplanationExecution{foreach($this->items as $item)if($item->id===$id)return $item;return null;} public function findByRequest(string $u,string $q,?string $a,string $v):?QuestionExplanationExecution{return $this->items[$this->key($u,$q,$a,$v)]??null;} public function findLatestForUserQuestion(string $u,string $q):?QuestionExplanationExecution{return null;} public function claimNextPending(\DateTimeImmutable $now):?QuestionExplanationJob{return null;} public function save(QuestionExplanationExecution $execution):void{$this->saved++;$this->items[$this->key($execution->userId,$execution->questionId,$execution->attemptId,$execution->algorithmVersion)]=$execution;} };
        $attempts = new class implements AttemptRepositoryInterface { public function saveAttempt(Attempt $attempt):void{} public function findByIdForUser(string $attemptId,string $userId):?Attempt{return null;} public function nextNumber(string $userId,string $notebookId,string $questionId):int{return 1;} public function complete(string $attemptId,string $finalAnswerId,\DateTimeImmutable $completedAt):bool{return false;} public function appendAnswer(Answer $answer):void{} public function listAnswers(string $attemptId):array{return [];} };
        $service = new RequestQuestionExplanationService($this->questions(), $attempts, $executions, new QuestionExplanationSafetyPolicy(), new QuestionExplanationResponseMapper());
        $now = new \DateTimeImmutable('2026-01-01T00:00:00Z');
        $first = $service->execute('user-a', new RequestQuestionExplanationRequestDto('question-1'), $now);
        $same = $service->execute('user-a', new RequestQuestionExplanationRequestDto('question-1'), $now);
        $other = $service->execute('user-b', new RequestQuestionExplanationRequestDto('question-1'), $now);
        self::assertSame($first->id, $same->id);
        self::assertNotSame($first->id, $other->id);
        self::assertSame(2, $executions->saved);
        self::assertSame('CONCEPTUAL_ONLY', $first->safetyMode);
    }

    private function questions(): FrozenQuestionReaderInterface
    {
        return new class implements FrozenQuestionReaderInterface { public function findByIds(array $ids):array{return array_map(static fn(string $id):PublishedQuestion=>new PublishedQuestion($id,'enunciado','EASY',null,null,[new PublishedQuestionOption('option-a','A','opção',1)]),$ids);} };
    }
}
