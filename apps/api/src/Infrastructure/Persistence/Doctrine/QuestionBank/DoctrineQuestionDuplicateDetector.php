<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Domain\QuestionBank\Repository\QuestionDuplicateDetectorInterface;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineQuestionDuplicateDetector implements QuestionDuplicateDetectorInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function findExistingByStatements(array $statements): array
    {
        $wanted = array_fill_keys(array_map($this->normalize(...), $statements), true);
        if ($wanted === []) return [];
        $records = $this->entityManager->createQueryBuilder()->select('question.id, question.statement')
            ->from(QuestionRecord::class, 'question')->getQuery()->getArrayResult();
        $matches = [];
        foreach ($records as $record) {
            $normalized = $this->normalize((string) $record['statement']);
            if (isset($wanted[$normalized])) $matches[$normalized] = (string) $record['id'];
        }
        return $matches;
    }

    private function normalize(string $statement): string
    {
        return mb_strtolower((string) preg_replace('/\s+/u', ' ', trim($statement)));
    }
}
