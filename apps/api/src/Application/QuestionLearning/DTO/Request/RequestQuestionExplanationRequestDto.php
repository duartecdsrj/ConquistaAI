<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Request;
final readonly class RequestQuestionExplanationRequestDto { public function __construct(public string $questionId, public ?string $attemptId = null) { if (trim($questionId) === '') throw new \InvalidArgumentException('Questão inválida.'); } }
