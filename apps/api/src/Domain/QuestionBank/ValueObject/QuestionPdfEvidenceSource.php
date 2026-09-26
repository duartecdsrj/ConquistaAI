<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\ValueObject;
final readonly class QuestionPdfEvidenceSource { /** @param list<int> $pages */ public function __construct(public string $questionId,public string $statement,public string $documentPath,public array $pages){} }
