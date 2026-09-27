<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
final readonly class QuestionCorrectionRequestResponseDto { public function __construct(public string $id,public string $questionId,public string $status,public string $instruction,public string $createdAt,public ?string $errorMessage,public ?array $proposal){} }
