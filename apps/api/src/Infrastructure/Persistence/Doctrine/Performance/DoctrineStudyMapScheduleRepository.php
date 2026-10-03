<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Performance;

use App\Domain\Performance\Entity\StudyMapScheduleItem;
use App\Domain\Performance\Enum\StudyScheduleStatus;
use App\Domain\Performance\Repository\StudyMapScheduleRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Performance\Entity\StudyMapScheduleDependencyRecord;
use App\Infrastructure\Persistence\Doctrine\Performance\Entity\StudyMapScheduleItemRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineStudyMapScheduleRepository implements StudyMapScheduleRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function listForUserExam(string $userId, string $examId): array
    {
        $items = $this->entityManager->getRepository(StudyMapScheduleItemRecord::class)->findBy(['userId' => $userId, 'examId' => $examId], ['startDate' => 'ASC']);
        $dependencies = $this->entityManager->getRepository(StudyMapScheduleDependencyRecord::class)->findBy(['userId' => $userId, 'examId' => $examId]);
        $bySubject = [];
        foreach ($dependencies as $dependency) {
            $bySubject[$dependency->taxonomySubjectId][] = $dependency->predecessorSubjectId;
        }
        return array_map(fn (StudyMapScheduleItemRecord $item): StudyMapScheduleItem => $this->map($item, $bySubject[$item->taxonomySubjectId] ?? []), $items);
    }

    public function save(StudyMapScheduleItem $item): void
    {
        $record = $this->entityManager->find(StudyMapScheduleItemRecord::class, ['userId' => $item->userId, 'examId' => $item->examId, 'taxonomySubjectId' => $item->taxonomySubjectId]) ?? new StudyMapScheduleItemRecord();
        $record->userId = $item->userId;
        $record->examId = $item->examId;
        $record->taxonomySubjectId = $item->taxonomySubjectId;
        $record->startDate = $item->startDate;
        $record->endDate = $item->endDate;
        $record->status = $item->status->value;
        $record->completedAt = $item->completedAt;
        $record->createdAt = $item->createdAt;
        $record->updatedAt = $item->updatedAt;
        $this->entityManager->persist($record);
        $existing = $this->entityManager->getRepository(StudyMapScheduleDependencyRecord::class)->findBy(['userId' => $item->userId, 'examId' => $item->examId, 'taxonomySubjectId' => $item->taxonomySubjectId]);
        foreach ($existing as $dependency) {
            $this->entityManager->remove($dependency);
        }
        foreach (array_values(array_unique($item->predecessorSubjectIds)) as $predecessorSubjectId) {
            $dependency = new StudyMapScheduleDependencyRecord();
            $dependency->userId = $item->userId;
            $dependency->examId = $item->examId;
            $dependency->taxonomySubjectId = $item->taxonomySubjectId;
            $dependency->predecessorSubjectId = $predecessorSubjectId;
            $this->entityManager->persist($dependency);
        }
    }

    /** @param list<string> $predecessors */
    private function map(StudyMapScheduleItemRecord $item, array $predecessors): StudyMapScheduleItem
    {
        return new StudyMapScheduleItem($item->userId, $item->examId, $item->taxonomySubjectId, $item->startDate, $item->endDate, StudyScheduleStatus::from($item->status), $item->completedAt, $predecessors, $item->createdAt, $item->updatedAt);
    }
}
