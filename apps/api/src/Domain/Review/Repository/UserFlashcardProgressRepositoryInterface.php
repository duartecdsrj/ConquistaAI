<?php
declare(strict_types=1);
namespace App\Domain\Review\Repository;
use App\Domain\Review\Entity\UserFlashcardProgress;
interface UserFlashcardProgressRepositoryInterface { public function find(string $userId,string $flashcardId):?UserFlashcardProgress; public function save(UserFlashcardProgress $progress):void; /** @param list<string> $retainedFlashcardIds */ public function archiveExcept(string $userId,array $retainedFlashcardIds,\DateTimeImmutable $now):int; /** @return list<UserFlashcardProgress> */ public function dueForUser(string $userId,\DateTimeImmutable $now,int $limit,array $excludedFlashcardIds=[]):array; /** @return list<UserFlashcardProgress> */ public function upcomingForUser(string $userId,\DateTimeImmutable $now,int $limit,array $excludedFlashcardIds=[]):array; public function countDueForUser(string $userId,\DateTimeImmutable $now):int; public function hasUpcomingForUser(string $userId,\DateTimeImmutable $now):bool; }
