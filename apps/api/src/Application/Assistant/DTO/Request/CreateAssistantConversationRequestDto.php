<?php
declare(strict_types=1);
namespace App\Application\Assistant\DTO\Request;
final readonly class CreateAssistantConversationRequestDto { public function __construct(public string $syllabusId, public ?string $title) {} }
