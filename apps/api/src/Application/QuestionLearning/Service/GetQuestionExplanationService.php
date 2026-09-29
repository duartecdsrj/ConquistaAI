<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Service;
use App\Application\QuestionLearning\DTO\Response\QuestionExplanationResponseDto;use App\Application\QuestionLearning\Mapper\QuestionExplanationResponseMapper;use App\Domain\QuestionLearning\Repository\QuestionExplanationExecutionRepositoryInterface;
final readonly class GetQuestionExplanationService { public function __construct(private QuestionExplanationExecutionRepositoryInterface $executions,private QuestionExplanationResponseMapper $mapper) {} public function execute(string $userId,string $questionId):?QuestionExplanationResponseDto{$execution=$this->executions->findLatestForUserQuestion($userId,$questionId);return $execution===null?null:$this->mapper->toResponse($execution);} }
