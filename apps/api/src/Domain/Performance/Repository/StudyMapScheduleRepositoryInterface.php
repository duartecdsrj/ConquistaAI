<?php
declare(strict_types=1);

namespace App\Domain\Performance\Repository;

use App\Domain\Performance\Entity\StudyMapScheduleItem;

interface StudyMapScheduleRepositoryInterface
{
    /** @return list<StudyMapScheduleItem> */
    public function listForUserExam(string $userId, string $examId): array;

    public function save(StudyMapScheduleItem $item): void;
}
