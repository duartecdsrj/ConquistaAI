<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Application\QuestionBank\DTO\Request\MarkQuestionsForApprovalRequestDto;
use App\Domain\QuestionBank\Repository\EditorialQuestionRepositoryInterface;
final class MarkQuestionsForApprovalService { public function __construct(private readonly EditorialQuestionRepositoryInterface $questions) {} public function mark(MarkQuestionsForApprovalRequestDto $request): int { return $this->questions->markForApproval($request->questionIds); } }
