<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Repository;

use App\Domain\QuestionBank\Entity\QuestionImportAnalysis;

interface QuestionImportAnalysisRepositoryInterface
{
    public function save(QuestionImportAnalysis $analysis): void;

    /** @return list<QuestionImportAnalysis> */
    public function listForJob(string $jobId): array;

    public function linkQuestion(string $analysisId, string $questionId): void;
}
