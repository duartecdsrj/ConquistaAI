<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\Mapper;
use App\Application\QuestionLearning\DTO\Response\QuestionInteractionResponseDto;use App\Application\QuestionLearning\DTO\Response\QuestionNoteResponseDto;use App\Domain\QuestionLearning\Entity\UserQuestionInteraction;
final readonly class QuestionInteractionResponseMapper { public function toResponse(UserQuestionInteraction $interaction, ?QuestionNoteResponseDto $note = null, int $commentCount = 0, int $ownOpenReportCount = 0): QuestionInteractionResponseDto { $state = $interaction->state(); return new QuestionInteractionResponseDto($interaction->questionId, $state->favorite, $state->reviewLater, $state->notMastered, $note, $commentCount, $ownOpenReportCount); } }
