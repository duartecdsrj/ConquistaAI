<?php
declare(strict_types=1);
namespace App\Domain\Study\Repository;
use App\Domain\Study\ReadModel\StudyContestSubject;
interface StudyContestSubjectRepositoryInterface { /** @return list<StudyContestSubject> */ public function listForPosition(string $positionId):array; }
