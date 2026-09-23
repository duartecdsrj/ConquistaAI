<?php
declare(strict_types=1);

namespace App\Domain\QuestionBank\Repository;

use App\Domain\QuestionBank\ReadModel\PublishedQuestionFilter;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionPage;

interface PublishedQuestionRepositoryInterface
{
    public function findPublished(PublishedQuestionFilter $filter): PublishedQuestionPage;
}
