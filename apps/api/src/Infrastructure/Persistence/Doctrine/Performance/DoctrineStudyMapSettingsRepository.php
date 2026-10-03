<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance;

use App\Domain\Performance\Entity\StudyMapSettings;
use App\Domain\Performance\Repository\StudyMapSettingsRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Performance\Entity\StudyMapSettingsRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineStudyMapSettingsRepository implements StudyMapSettingsRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function findForUserExam(string $userId, string $examId): ?StudyMapSettings
    {
        $record = $this->entityManager->find(StudyMapSettingsRecord::class, ['userId' => $userId, 'examId' => $examId]);
        return $record === null ? null : new StudyMapSettings($record->userId, $record->examId, $record->examDate, $record->createdAt, $record->updatedAt);
    }

    public function save(StudyMapSettings $settings): void
    {
        $record = $this->entityManager->find(StudyMapSettingsRecord::class, ['userId' => $settings->userId, 'examId' => $settings->examId]) ?? new StudyMapSettingsRecord();
        $record->userId = $settings->userId;
        $record->examId = $settings->examId;
        $record->examDate = $settings->examDate;
        $record->createdAt = $settings->createdAt;
        $record->updatedAt = $settings->updatedAt;
        $this->entityManager->persist($record);
    }
}
