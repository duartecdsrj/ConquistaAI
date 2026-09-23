<?php
declare(strict_types=1);
namespace App\Domain\Study\Repository;
use App\Domain\Study\ReadModel\CompletedNotebookAnswer;
interface NotebookProgressReaderInterface { /** @return list<CompletedNotebookAnswer> */ public function completedAnswersForNotebook(string $userId,string $notebookId):array; }
