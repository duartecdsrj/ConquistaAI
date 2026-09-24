<?php
declare(strict_types=1);
namespace App\Application\Assistant\DTO\Request;
final readonly class CreateAssistantMessageRequestDto { public function __construct(public string $conversationId, public string $content) {} }
