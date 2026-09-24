<?php
declare(strict_types=1);
namespace App\Domain\Assistant\Entity;
final readonly class AssistantConversation { public function __construct(public string $id, public string $userId, public string $syllabusId, public string $title, public \DateTimeImmutable $createdAt, public \DateTimeImmutable $updatedAt) { if (trim($title)==='') throw new \DomainException('O titulo da conversa e obrigatorio.'); } }
