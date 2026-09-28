<?php
declare(strict_types=1);
namespace App\Domain\Review\Repository;
use App\Domain\Review\Entity\NotebookAnalysisAction;
interface NotebookAnalysisActionRepositoryInterface { public function save(NotebookAnalysisAction $action):void; /** @return list<NotebookAnalysisAction> */ public function listForExecution(string $executionId):array; }
