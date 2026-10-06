<?php
declare(strict_types=1);
namespace App\Domain\Review\Entity;
use App\Domain\Review\ValueObject\FlashcardProgressState;
final class UserFlashcardProgress { public function __construct(public readonly string $userId,public readonly string $flashcardId,private FlashcardProgressState $state,public ?\DateTimeImmutable $lastReviewedAt,public readonly \DateTimeImmutable $createdAt,public \DateTimeImmutable $updatedAt,public bool $active=true){} public function state():FlashcardProgressState{return $this->state;} public function apply(FlashcardProgressState $next,\DateTimeImmutable $now):void{$this->state=$next;$this->lastReviewedAt=$now;$this->updatedAt=$now;} public function activate(\DateTimeImmutable $now):void{$this->active=true;$this->updatedAt=$now;} }
