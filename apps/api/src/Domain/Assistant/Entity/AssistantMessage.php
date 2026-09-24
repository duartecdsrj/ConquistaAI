<?php
declare(strict_types=1);
namespace App\Domain\Assistant\Entity;
use App\Domain\Assistant\ValueObject\SyllabusEvidence;
final readonly class AssistantMessage { /** @param list<SyllabusEvidence> $evidence */ public function __construct(public string $id, public string $conversationId, public string $role, public string $content, public ?string $provider, public ?string $model, public array $evidence, public \DateTimeImmutable $createdAt) { if (!in_array($role,['USER','ASSISTANT'],true)||trim($content)==='') throw new \DomainException('Mensagem invalida.'); } }
