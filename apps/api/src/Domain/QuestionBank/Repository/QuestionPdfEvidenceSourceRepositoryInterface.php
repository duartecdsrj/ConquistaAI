<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Repository;
use App\Domain\QuestionBank\ValueObject\QuestionPdfEvidenceSource;
interface QuestionPdfEvidenceSourceRepositoryInterface { public function findPublishedSource(string $questionId):?QuestionPdfEvidenceSource; }
