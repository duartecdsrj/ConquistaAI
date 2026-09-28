<?php
declare(strict_types=1);
namespace App\Domain\Review\Repository;
use App\Domain\Review\Entity\UserConceptMastery;
interface UserConceptMasteryRepositoryInterface { public function find(string $userId,string $taxonomySubjectId):?UserConceptMastery; public function save(UserConceptMastery $mastery):void; /** @return list<UserConceptMastery> */ public function listForUser(string $userId,int $offset,int $limit):array; public function countForUser(string $userId):int; }
