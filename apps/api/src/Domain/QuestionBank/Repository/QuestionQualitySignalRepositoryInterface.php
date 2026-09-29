<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Repository;

use App\Domain\QuestionBank\Entity\QuestionQualitySignal;

interface QuestionQualitySignalRepositoryInterface
{
    public function replace(QuestionQualitySignal $signal): void;
    public function findForQuestion(string $questionId): ?QuestionQualitySignal;
}
