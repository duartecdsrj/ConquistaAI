<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Response;
final readonly class QuestionNoteResponseDto { public function __construct(public string $questionId, public string $content, public string $createdAt, public string $updatedAt) {} }
