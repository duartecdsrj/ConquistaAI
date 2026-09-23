<?php
declare(strict_types=1);
namespace Tests\Unit\Performance;
use App\Application\Performance\DTO\Request\AppendAnswerRequestDto;
use App\Application\Performance\Service\AppendAnswerService;
use App\Domain\Performance\Entity\Answer;
use App\Domain\Performance\Entity\Attempt;
use App\Domain\Performance\Repository\AttemptRepositoryInterface;
use PHPUnit\Framework\TestCase;
final class AppendAnswerServiceTest extends TestCase { public function testItAppendsAnImmutableAnswer(): void { $repository = new AppendAnswerRepository(); $answer=(new AppendAnswerService($repository))->append(new AppendAnswerRequestDto("attempt", "option", 1, 4)); self::assertSame($answer,$repository->answer); } }
final class AppendAnswerRepository implements AttemptRepositoryInterface { public ?Answer $answer=null; public function saveAttempt(Attempt $attempt): void {} public function appendAnswer(Answer $answer): void {$this->answer=$answer;} public function listAnswers(string $attemptId): array{return $this->answer===null?[]:[$this->answer];} }
