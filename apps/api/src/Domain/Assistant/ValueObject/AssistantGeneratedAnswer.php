<?php
declare(strict_types=1);
namespace App\Domain\Assistant\ValueObject;
final readonly class AssistantGeneratedAnswer { /** @param list<SyllabusEvidence> $evidence */ public function __construct(public string $content, public string $provider, public string $model, public array $evidence) {} }
