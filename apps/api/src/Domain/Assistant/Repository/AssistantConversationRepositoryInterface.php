<?php
declare(strict_types=1);
namespace App\Domain\Assistant\Repository;
use App\Domain\Assistant\Entity\AssistantConversation;use App\Domain\Assistant\Entity\AssistantMessage;
interface AssistantConversationRepositoryInterface { public function saveConversation(AssistantConversation $conversation):void; public function findConversationForUser(string $id,string $userId):?AssistantConversation; /** @return list<AssistantConversation> */ public function listConversationsForUser(string $userId):array; public function saveMessage(AssistantMessage $message):void; /** @return list<AssistantMessage> */ public function listMessagesForConversation(string $conversationId):array; }
