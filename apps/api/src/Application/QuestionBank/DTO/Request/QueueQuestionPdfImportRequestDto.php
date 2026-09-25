<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Request;
final readonly class QueueQuestionPdfImportRequestDto { public function __construct(public string $userId,public string $originalName,public string $mimeType,public string $contents){} }
