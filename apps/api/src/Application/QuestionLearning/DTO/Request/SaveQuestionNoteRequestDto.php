<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Request;
final readonly class SaveQuestionNoteRequestDto { public function __construct(public string $questionId, public string $content) { if (trim($questionId) === '' || mb_strlen(trim($content)) < 1 || mb_strlen($content) > 5000) { throw new \InvalidArgumentException('Anotação inválida.'); } } }
