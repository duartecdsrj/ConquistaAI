<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Request;
final readonly class CreateQuestionCorrectionRequestDto { public function __construct(public string $questionId,public string $requestedBy,public string $instruction){} }
