<?php
declare(strict_types=1);
namespace App\Application\Assistant\Mapper;
use App\Application\Assistant\DTO\Response\AssistantConversationResponseDto;use App\Application\Assistant\DTO\Response\AssistantMessageResponseDto;use App\Domain\Assistant\Entity\AssistantConversation;use App\Domain\Assistant\Entity\AssistantMessage;
final class AssistantResponseMapper { public function conversation(AssistantConversation $c):AssistantConversationResponseDto{return new AssistantConversationResponseDto($c->id,$c->syllabusId,$c->title,$c->createdAt->format(DATE_ATOM),$c->updatedAt->format(DATE_ATOM));} public function message(AssistantMessage $m):AssistantMessageResponseDto{return new AssistantMessageResponseDto($m->id,$m->conversationId,$m->role,$m->content,$m->provider,$m->model,array_map(static fn($e)=>['pageNumber'=>$e->pageNumber,'excerpt'=>$e->excerpt],$m->evidence),$m->createdAt->format(DATE_ATOM));} }
