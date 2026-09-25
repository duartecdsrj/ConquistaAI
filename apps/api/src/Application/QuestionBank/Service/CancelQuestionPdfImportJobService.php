<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Domain\QuestionBank\Repository\QuestionPdfImportJobRepositoryInterface;
final class CancelQuestionPdfImportJobService { public function __construct(private readonly QuestionPdfImportJobRepositoryInterface $jobs) {} public function cancel(string $id,string $userId): bool { return $this->jobs->cancelForUser($id,$userId); } }
