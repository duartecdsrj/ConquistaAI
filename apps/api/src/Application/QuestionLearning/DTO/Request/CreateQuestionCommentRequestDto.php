<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Request;
final readonly class CreateQuestionCommentRequestDto { public function __construct(public string $questionId, public string $content, public ?string $parentId = null) { if (trim($questionId) === '' || mb_strlen(trim($content)) < 1 || mb_strlen($content) > 2000) { throw new \InvalidArgumentException('Comentário inválido.'); } } }
