<?php
declare(strict_types=1);
namespace App\Application\Assistant\DTO\Response;
final readonly class AssistantMessageResponseDto { /** @param list<array{pageNumber:int,excerpt:string}> $evidence */ public function __construct(public string $id,public string $conversationId,public string $role,public string $content,public ?string $provider,public ?string $model,public array $evidence,public string $createdAt) {} }
