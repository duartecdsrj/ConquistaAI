<?php
declare(strict_types=1);
namespace App\Application\Assistant\Service;
use App\Application\Assistant\DTO\Response\AssistantConversationResponseDto;use App\Application\Assistant\Mapper\AssistantResponseMapper;use App\Domain\Assistant\Repository\AssistantConversationRepositoryInterface;
final class ListAssistantConversationsService { public function __construct(private readonly AssistantConversationRepositoryInterface $conversations,private readonly AssistantResponseMapper $mapper){} /** @return list<AssistantConversationResponseDto> */ public function listForUser(string $userId):array{return array_map($this->mapper->conversation(...),$this->conversations->listConversationsForUser($userId));} }
