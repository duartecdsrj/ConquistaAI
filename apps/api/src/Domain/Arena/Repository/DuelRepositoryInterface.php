<?php
declare(strict_types=1);

namespace App\Domain\Arena\Repository;

use App\Domain\Arena\Entity\Duel;
use App\Domain\Arena\Entity\DuelAnswer;
use App\Domain\Arena\ReadModel\ArenaRoomSummary;

interface DuelRepositoryInterface
{
    public function save(Duel $duel): void;
    public function findForParticipant(string $duelId, string $userId): ?Duel;
    public function findByCode(string $code): ?Duel;
    public function findPublicWaiting(string $duelId): ?Duel;
    /**  list<ArenaRoomSummary> */ public function listPublicWaiting(int $offset,int $limit): array;
    /**  list<ArenaRoomSummary> */ public function listPrivateWaitingCreatedBy(string $userId,int $offset,int $limit): array;
    public function countPublicWaiting(): int;
    public function countPrivateWaitingCreatedBy(string $userId): int;
    public function addParticipant(string $duelId, string $userId, \DateTimeImmutable $joinedAt): bool;
    /** @param list<string> $taxonomySubjectIds */
    public function replaceParticipantSubjects(string $duelId, string $userId, array $taxonomySubjectIds, \DateTimeImmutable $readyAt): void;
    /** @return list<string> */
    public function participantSubjectIds(string $duelId): array;
    /** @return list<string> */
    public function recentQuestionIdsForParticipants(string $duelId): array;
    /** @param list<string> $questionIds */
    public function freezeQuestions(string $duelId, array $questionIds): void;
    public function appendAnswer(DuelAnswer $answer): bool;
    public function participantCount(string $duelId): int;
    public function readyParticipantCount(string $duelId): int;
}
