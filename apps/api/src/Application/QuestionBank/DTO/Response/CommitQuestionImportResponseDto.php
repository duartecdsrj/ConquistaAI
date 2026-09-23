<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
final readonly class CommitQuestionImportResponseDto { public function __construct(public string $importId,public int $createdQuestions){} }
