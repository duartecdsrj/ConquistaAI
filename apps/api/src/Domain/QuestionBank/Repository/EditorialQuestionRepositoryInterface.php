<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Repository;
use App\Domain\QuestionBank\ReadModel\PublishedQuestion;
interface EditorialQuestionRepositoryInterface {
 /** @return array{items:list<PublishedQuestion>,total:int} */ public function listDrafts(int $offset,int $limit):array;
 public function publish(string $id): bool;
}
