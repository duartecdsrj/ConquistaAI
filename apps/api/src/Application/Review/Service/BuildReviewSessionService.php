<?php
declare(strict_types=1);
namespace App\Application\Review\Service;
use App\Domain\Review\Entity\ReviewSession;use App\Domain\Review\Enum\ReviewSessionKind;use App\Domain\Review\Enum\ReviewSessionStatus;use App\Domain\Review\Repository\FlashcardRepositoryInterface;use App\Domain\Review\Repository\ReviewSessionRepositoryInterface;use App\Domain\Review\Repository\UserConceptMasteryRepositoryInterface;use App\Domain\Review\Repository\UserFlashcardProgressRepositoryInterface;use App\Domain\Review\Service\ReviewPriorityCalculatorInterface;
final readonly class BuildReviewSessionService {
 public function __construct(private ReviewSessionRepositoryInterface $sessions,private UserFlashcardProgressRepositoryInterface $progresses,private UserConceptMasteryRepositoryInterface $mastery,private FlashcardRepositoryInterface $cards,private ReviewPriorityCalculatorInterface $priorities){}
 public function execute(string $userId,ReviewSessionKind $kind,int $limit,\DateTimeImmutable $now):ReviewSession {
  if($limit<1||$limit>100)throw new \InvalidArgumentException('Limite de revisão inválido.');
  $active=$this->sessions->findActive($userId,$kind);if($active!==null){$active->cards=$this->resolve($active->flashcardIds);return $active;}
  $candidates=[];foreach($this->progresses->dueForUser($userId,$now,100) as $progress){$card=$this->cards->findById($progress->flashcardId);if($card===null)continue;$mastery=$this->mastery->find($userId,$card->primaryTaxonomySubjectId);$candidates[]=['id'=>$card->id,'score'=>$this->priorities->calculate($progress,$mastery?->masteryScore,0,0,$now)];}
  usort($candidates,static fn(array $a,array $b):int=>$b['score']<=>$a['score']);$ids=array_column(array_slice($candidates,0,$limit),'id');$session=new ReviewSession($this->uuid(),$userId,$kind,ReviewSessionStatus::ACTIVE,$limit,$now,flashcardIds:$ids,cards:$this->resolve($ids));$this->sessions->save($session);return $session;
 }
 private function resolve(array $ids):array{return array_values(array_filter(array_map(fn(string $id)=>$this->cards->findById($id),$ids)));}
 private function uuid():string{$b=random_bytes(16);$b[6]=chr((ord($b[6])&15)|64);$b[8]=chr((ord($b[8])&63)|128);return vsprintf('%s%s-%s-%s-%s-%s%s%s',str_split(bin2hex($b),4));}
}
