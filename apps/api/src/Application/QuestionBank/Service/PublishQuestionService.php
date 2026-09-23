<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Domain\QuestionBank\Repository\EditorialQuestionRepositoryInterface;
final class PublishQuestionService {public function __construct(private readonly EditorialQuestionRepositoryInterface $questions){}public function publish(string $id):bool{return $this->questions->publish($id);}}
