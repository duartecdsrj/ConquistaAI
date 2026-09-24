<?php
declare(strict_types=1);
namespace App\Application\Assistant\Service;
use App\Application\Assistant\DTO\Response\AssistantMessageResponseDto;use App\Application\Assistant\Mapper\AssistantResponseMapper;use App\Domain\Assistant\Repository\AssistantConversationRepositoryInterface;
final class ListAssistantMessagesService { public function __construct(private readonly AssistantConversationRepositoryInterface $conversations,private readonly AssistantResponseMapper $mapper){} /** @return list<AssistantMessageResponseDto> */ public function listForUser(string $userId,string $conversationId):array {if($this->conversations->findConversationForUser($conversationId,$userId)===null)throw new \DomainException('Conversa nao encontrada.');return array_map($this->mapper->message(...),$this->conversations->listMessagesForConversation($conversationId));} }
