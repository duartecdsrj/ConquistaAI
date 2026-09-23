<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Domain\QuestionBank\Repository\ImportedQuestionWriterInterface;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineImportedQuestionWriter implements ImportedQuestionWriterInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function createFromImport(string $syllabusId, string $createdBy, array $rows): int
    {
        $created = 0;
        foreach ($rows as $row) {
            $question = new QuestionRecord();
            $question->id = $this->id();
            $question->syllabusId = $syllabusId;
            $question->statement = trim((string) $row['statement']);
            $question->difficulty = in_array($row['difficulty'] ?? null, ['EASY', 'MEDIUM', 'HARD'], true) ? $row['difficulty'] : 'MEDIUM';
            $question->board = is_string($row['board'] ?? null) ? $row['board'] : null;
            $question->examYear = is_int($row['year'] ?? null) ? $row['year'] : null;
            $question->status = 'DRAFT';
            $question->createdBy = $createdBy;
            $question->createdAt = new \DateTimeImmutable('now');
            $question->updatedAt = $question->createdAt;
            $this->entityManager->persist($question);

            foreach ($row['options'] as $index => $option) {
                $record = new QuestionOptionRecord();
                $record->id = $this->id();
                $record->questionId = $question->id;
                $record->label = substr((string) ($option['id'] ?? chr(65 + $index)), 0, 1);
                $record->content = (string) ($option['content'] ?? $option['label'] ?? '');
                $record->sortOrder = $index + 1;
                $record->createdAt = new \DateTimeImmutable('now');
                $this->entityManager->persist($record);
                if ((string) ($option['id'] ?? '') === (string) $row['correct_option']) {
                    $question->correctOptionId = $record->id;
                }
            }
            $created++;
        }

        return $created;
    }

    private function id(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
