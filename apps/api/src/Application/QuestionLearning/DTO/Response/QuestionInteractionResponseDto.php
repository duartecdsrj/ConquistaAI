<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Response;
final readonly class QuestionInteractionResponseDto { public function __construct(public string $questionId, public bool $favorite, public bool $reviewLater, public bool $notMastered, public ?QuestionNoteResponseDto $note = null, public int $commentCount = 0, public int $ownOpenReportCount = 0) {} }
