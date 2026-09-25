<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Repository;
use App\Domain\QuestionBank\Entity\QuestionPdfImportJob;
interface QuestionPdfImportJobRepositoryInterface { public function save(QuestionPdfImportJob $job):void; public function findByIdForUser(string $id,string $userId):?QuestionPdfImportJob; public function claimNext():?QuestionPdfImportJob; }
