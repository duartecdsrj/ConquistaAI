<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Response;
final readonly class QuestionCommentResponseDto { public function __construct(public string $id, public string $questionId, public string $authorUserId, public ?string $parentId, public string $content, public string $createdAt) {} }
