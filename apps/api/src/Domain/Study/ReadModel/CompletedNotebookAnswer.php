<?php
declare(strict_types=1);
namespace App\Domain\Study\ReadModel;
final readonly class CompletedNotebookAnswer { public function __construct(public string $questionId,public bool $isCorrect,public int $elapsedSeconds) {} }
