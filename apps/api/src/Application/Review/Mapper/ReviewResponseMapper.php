<?php
declare(strict_types=1);
namespace App\Application\Review\Mapper;
use App\Application\Review\DTO\Response\ReviewSessionResponseDto;use App\Domain\Review\Entity\Flashcard;use App\Domain\Review\Entity\ReviewSession;
final class ReviewResponseMapper { public function session(ReviewSession $s,int $overdueCards,bool $canAdvance,bool $currentCardReviewed=false):ReviewSessionResponseDto{return new ReviewSessionResponseDto($s->id,$s->kind->value,$s->status->value,$s->requestedLimit,array_map(static fn(Flashcard $card)=>['id'=>$card->id,'front'=>$card->front,'back'=>$card->back,'conceptId'=>$card->primaryTaxonomySubjectId],$s->cards),$overdueCards,$canAdvance,$s->canGoPrevious,$s->canGoNext,$currentCardReviewed);} }
