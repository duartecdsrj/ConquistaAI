<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Request;
final readonly class CommitQuestionImportRequestDto { public function __construct(public string $userId,public string $importId,public string $syllabusId){} }
