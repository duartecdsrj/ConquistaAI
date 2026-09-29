<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Response;
final readonly class QuestionExplanationResponseDto { public function __construct(public string $id,public string $questionId,public ?string $attemptId,public string $status,public string $safetyMode,public string $requestedAt,public ?string $completedAt,public ?array $result,public ?string $errorMessage) {} }
