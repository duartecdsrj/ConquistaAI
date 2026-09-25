<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Catalog;

use App\Domain\Catalog\Entity\Exam;
use App\Domain\Catalog\Repository\ExamRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Catalog\Entity\ExamRecord;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineExamRepository implements ExamRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(Exam $exam): void
    {
        $record = new ExamRecord();
        $record->id = $exam->id;
        $record->name = $exam->name;
        $record->organizer = $exam->organizer;
        $record->year = $exam->year;
        $record->institutionLogoUrl = $exam->institutionLogoUrl;
        $record->organizerLogoUrl = $exam->organizerLogoUrl;
        $record->createdAt = new DateTimeImmutable('now');
        $record->updatedAt = $record->createdAt;

        $this->entityManager->persist($record);
    }

    public function findById(string $id): ?Exam
    {
        $record = $this->entityManager->find(ExamRecord::class, $id);

        return $record instanceof ExamRecord ? $this->map($record) : null;
    }

    public function update(Exam $exam): bool
    {
        $record = $this->entityManager->find(ExamRecord::class, $exam->id);
        if (!$record instanceof ExamRecord) {
            return false;
        }

        $record->name = $exam->name;
        $record->organizer = $exam->organizer;
        $record->year = $exam->year;
        $record->institutionLogoUrl = $exam->institutionLogoUrl;
        $record->organizerLogoUrl = $exam->organizerLogoUrl;
        $record->updatedAt = new DateTimeImmutable('now');

        return true;
    }

    public function deleteById(string $id): bool
    {
        $record = $this->entityManager->find(ExamRecord::class, $id);
        if (!$record instanceof ExamRecord) {
            return false;
        }

        $this->entityManager->remove($record);

        return true;
    }

    /** @return list<Exam> */
    public function list(): array
    {
        return array_map(
            fn (ExamRecord $record): Exam => $this->map($record),
            $this->entityManager->createQueryBuilder()
                ->select('exam')
                ->from(ExamRecord::class, 'exam')
                ->orderBy('exam.year', 'DESC')
                ->addOrderBy('exam.name', 'ASC')
                ->getQuery()
                ->getResult(),
        );
    }

    private function map(ExamRecord $record): Exam
    {
        return new Exam($record->id, $record->name, $record->organizer, $record->year, $record->institutionLogoUrl, $record->organizerLogoUrl);
    }
}
