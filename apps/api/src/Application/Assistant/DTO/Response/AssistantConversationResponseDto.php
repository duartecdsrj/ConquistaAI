<?php
declare(strict_types=1);
namespace App\Application\Assistant\DTO\Response;
final readonly class AssistantConversationResponseDto { public function __construct(public string $id,public string $syllabusId,public string $title,public string $createdAt,public string $updatedAt) {} }
