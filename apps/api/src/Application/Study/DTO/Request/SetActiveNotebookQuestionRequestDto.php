<?php
declare(strict_types=1);
namespace App\Application\Study\DTO\Request;
final readonly class SetActiveNotebookQuestionRequestDto { public function __construct(public string $notebookId, public string $questionId) {} }
