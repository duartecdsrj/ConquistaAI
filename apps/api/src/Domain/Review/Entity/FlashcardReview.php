<?php
declare(strict_types=1);
namespace App\Domain\Review\Entity;
use App\Domain\Review\Enum\ReviewRating;use App\Domain\Review\ValueObject\FlashcardProgressState;
final readonly class FlashcardReview { public function __construct(public string $id,public string $userId,public string $flashcardId,public ?string $sessionId,public ReviewRating $rating,public FlashcardProgressState $previousState,public FlashcardProgressState $nextState,public \DateTimeImmutable $reviewedAt){} }
