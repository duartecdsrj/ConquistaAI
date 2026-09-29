<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Mapper;
use App\Application\QuestionLearning\DTO\Response\QuestionCommentResponseDto;use App\Domain\QuestionLearning\Entity\QuestionComment;
final readonly class QuestionCommentResponseMapper { public function toResponse(QuestionComment $comment): QuestionCommentResponseDto { return new QuestionCommentResponseDto($comment->id, $comment->questionId, $comment->authorUserId, $comment->parentId, $comment->content, $comment->createdAt->format(DATE_ATOM)); } }
