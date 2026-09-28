<?php
declare(strict_types=1);

namespace App\Application\Review\DTO\Request;

use App\Domain\Review\Enum\ReviewRating;

final readonly class RateFlashcardInputRequestDto
{
    public function __construct(public string $sessionId, public string $flashcardId, public ReviewRating $rating) {}
}
