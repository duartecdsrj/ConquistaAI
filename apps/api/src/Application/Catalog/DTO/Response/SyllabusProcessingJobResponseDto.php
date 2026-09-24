<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Response;
final readonly class SyllabusProcessingJobResponseDto { public function __construct(public string $id,public string $syllabusId,public string $documentSha256,public string $status,public int $progress,public ?string $errorMessage,public string $createdAt,public ?string $startedAt,public ?string $finishedAt){} }
