<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Application\QuestionBank\Mapper\PublishedQuestionResponseMapper;use App\Domain\QuestionBank\Repository\EditorialQuestionRepositoryInterface;
final class ListDraftQuestionsService {public function __construct(private readonly EditorialQuestionRepositoryInterface $questions,private readonly PublishedQuestionResponseMapper $mapper){} public function list(int $page,int $perPage):array{$result=$this->questions->listDrafts(($page-1)*$perPage,$perPage);return ['items'=>array_map($this->mapper->toResponse(...),$result['items']),'total'=>$result['total'],'page'=>$page,'perPage'=>$perPage];}}
