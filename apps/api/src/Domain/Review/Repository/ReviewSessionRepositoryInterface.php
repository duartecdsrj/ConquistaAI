<?php
declare(strict_types=1);
namespace App\Domain\Review\Repository;
use App\Domain\Review\Entity\ReviewSession;use App\Domain\Review\Enum\ReviewSessionKind;
interface ReviewSessionRepositoryInterface { public function findActive(string $userId,ReviewSessionKind $kind):?ReviewSession; public function findForUser(string $id,string $userId):?ReviewSession; public function save(ReviewSession $session):void; public function serveCard(ReviewSession $session,string $flashcardId):void; }
