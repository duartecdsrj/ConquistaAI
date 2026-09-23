<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Repository;

use App\Domain\QuestionBank\ReadModel\PublishedQuestion;

interface FrozenQuestionReaderInterface
{
    /** @param list<string> $ids @return list<PublishedQuestion> */
    public function findByIds(array $ids): array;
}
