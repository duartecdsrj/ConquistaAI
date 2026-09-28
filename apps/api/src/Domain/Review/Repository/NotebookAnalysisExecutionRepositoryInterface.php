<?php
declare(strict_types=1);
namespace App\Domain\Review\Repository;
use App\Domain\Review\Entity\NotebookAnalysisExecution;
use App\Domain\Review\ValueObject\NotebookAnalysisJob;
interface NotebookAnalysisExecutionRepositoryInterface { public function findByNotebookAndVersion(string $notebookId,string $algorithmVersion):?NotebookAnalysisExecution; public function findForNotebookUser(string $notebookId,string $userId):?NotebookAnalysisExecution; public function claimNextPending(\DateTimeImmutable $now):?NotebookAnalysisJob; public function save(NotebookAnalysisExecution $execution):void; }
