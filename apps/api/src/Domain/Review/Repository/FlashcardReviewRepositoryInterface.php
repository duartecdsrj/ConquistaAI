<?php
declare(strict_types=1);
namespace App\Domain\Review\Repository;
use App\Domain\Review\Entity\FlashcardReview;
interface FlashcardReviewRepositoryInterface { public function hasSessionReview(string $sessionId,string $flashcardId):bool; public function append(FlashcardReview $review):void; }
