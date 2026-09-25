<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Request;
final readonly class MarkQuestionsForApprovalRequestDto { /** @param list<string> $questionIds */ public function __construct(public array $questionIds) {} }
