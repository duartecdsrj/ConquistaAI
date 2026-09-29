<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Response;
final readonly class QuestionProblemReportResponseDto { public function __construct(public string $id, public string $questionId, public string $category, public string $description, public string $status, public string $createdAt, public string $updatedAt) {} }
