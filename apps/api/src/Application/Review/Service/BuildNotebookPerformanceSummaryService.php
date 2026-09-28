<?php
declare(strict_types=1);
namespace App\Application\Review\Service;
use App\Application\Review\DTO\Response\NotebookPerformanceSummaryDto;use App\Domain\Study\Repository\NotebookProgressReaderInterface;
final readonly class BuildNotebookPerformanceSummaryService { public function __construct(private NotebookProgressReaderInterface $progress){} public function execute(string $userId,string $notebookId):NotebookPerformanceSummaryDto{$answers=$this->progress->completedAnswersForNotebook($userId,$notebookId);$items=array_map(static fn($a)=>['question_id'=>$a->questionId,'is_correct'=>$a->isCorrect,'elapsed_seconds'=>$a->elapsedSeconds],$answers);return new NotebookPerformanceSummaryDto($notebookId,count($items),count(array_filter($items,static fn(array $a):bool=>$a['is_correct'])),$items);} }
