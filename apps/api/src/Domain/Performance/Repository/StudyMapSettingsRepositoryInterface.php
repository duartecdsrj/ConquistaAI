<?php
declare(strict_types=1);

namespace App\Domain\Performance\Repository;

use App\Domain\Performance\Entity\StudyMapSettings;

interface StudyMapSettingsRepositoryInterface
{
    public function findForUserExam(string $userId, string $examId): ?StudyMapSettings;

    public function save(StudyMapSettings $settings): void;
}
