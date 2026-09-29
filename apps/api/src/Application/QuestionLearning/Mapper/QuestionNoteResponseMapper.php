<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Mapper;
use App\Application\QuestionLearning\DTO\Response\QuestionNoteResponseDto;use App\Domain\QuestionLearning\Entity\QuestionNote;
final readonly class QuestionNoteResponseMapper { public function toResponse(QuestionNote $note): QuestionNoteResponseDto { return new QuestionNoteResponseDto($note->questionId, $note->content(), $note->createdAt->format(DATE_ATOM), $note->updatedAt()->format(DATE_ATOM)); } }
