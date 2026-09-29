<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Mapper;
use App\Application\QuestionLearning\DTO\Response\QuestionExplanationResponseDto;use App\Domain\QuestionLearning\Entity\QuestionExplanationExecution;
final readonly class QuestionExplanationResponseMapper { public function toResponse(QuestionExplanationExecution $e):QuestionExplanationResponseDto{return new QuestionExplanationResponseDto($e->id,$e->questionId,$e->attemptId,$e->status()->value,$e->safetyMode->value,$e->requestedAt->format(DATE_ATOM),$e->completedAt()?->format(DATE_ATOM),$e->result(),$e->status()->value==='FAILED'?$e->errorMessage():null);} }
