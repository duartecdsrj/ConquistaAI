<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Entity;
final readonly class SyllabusProcessingJob { public function __construct(public string $id,public string $syllabusId,public string $documentSha256,public string $status,public int $progress,public ?string $errorMessage,public \DateTimeImmutable $createdAt,public ?\DateTimeImmutable $startedAt=null,public ?\DateTimeImmutable $finishedAt=null){} }
