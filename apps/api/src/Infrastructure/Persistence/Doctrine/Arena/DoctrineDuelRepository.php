<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Arena;

use App\Domain\Arena\Entity\Duel;
use App\Domain\Arena\Entity\DuelAnswer;
use App\Domain\Arena\Enum\DuelStatus;
use App\Domain\Arena\Enum\DuelVisibility;
use App\Domain\Arena\Repository\DuelRepositoryInterface;
use App\Domain\Arena\ReadModel\ArenaRoomSummary;
use App\Infrastructure\Persistence\Doctrine\Arena\Entity\DuelAnswerRecord;
use App\Infrastructure\Persistence\Doctrine\Arena\Entity\DuelPlayerRecord;
use App\Infrastructure\Persistence\Doctrine\Arena\Entity\DuelQuestionRecord;
use App\Infrastructure\Persistence\Doctrine\Arena\Entity\DuelRecord;
use App\Infrastructure\Persistence\Doctrine\Arena\Entity\DuelSubjectRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineDuelRepository implements DuelRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}
    public function save(Duel $d): void { $r=$this->em->find(DuelRecord::class,$d->id)??new DuelRecord(); foreach(['id','code','creatorUserId','maxPlayers','subjectsPerPlayer','questionCount','questionSeconds','currentPosition','openedAt','deadlineAt','createdAt','finishedAt'] as $p){$r->$p=$d->$p;} $r->status=$d->status->value; $r->visibility=$d->visibility->value; $this->em->persist($r); }
    public function findForParticipant(string $id,string $userId): ?Duel { return $this->em->find(DuelPlayerRecord::class,['duelId'=>$id,'userId'=>$userId]) instanceof DuelPlayerRecord ? $this->map($this->em->find(DuelRecord::class,$id)) : null; }
    public function findByCode(string $code): ?Duel { $r=$this->em->getRepository(DuelRecord::class)->findOneBy(['code'=>strtoupper(trim($code)),'visibility'=>'PRIVATE']); return $r instanceof DuelRecord?$this->map($r):null; }
    public function findPublicWaiting(string $duelId): ?Duel { $r=$this->em->find(DuelRecord::class,$duelId); return $r instanceof DuelRecord&&$r->visibility==='PUBLIC'&&$r->status==='WAITING'?$this->map($r):null; }
    public function listPublicWaiting(int $offset,int $limit): array { return $this->listWaiting(['visibility'=>'PUBLIC'],$offset,$limit); }
    public function listPrivateWaitingCreatedBy(string $userId,int $offset,int $limit): array { return $this->listWaiting(['visibility'=>'PRIVATE','creatorUserId'=>$userId],$offset,$limit); }
    public function countPublicWaiting(): int { return $this->countWaiting(['visibility'=>'PUBLIC']); }
    public function countPrivateWaitingCreatedBy(string $userId): int { return $this->countWaiting(['visibility'=>'PRIVATE','creatorUserId'=>$userId]); }
    public function addParticipant(string $duelId,string $userId,\DateTimeImmutable $joinedAt): bool { if($this->em->find(DuelPlayerRecord::class,['duelId'=>$duelId,'userId'=>$userId]))return false; $p=new DuelPlayerRecord();$p->duelId=$duelId;$p->userId=$userId;$p->joinedAt=$joinedAt;$this->em->persist($p);return true; }
    public function replaceParticipantSubjects(string $duelId,string $userId,array $ids,\DateTimeImmutable $readyAt): void { $p=$this->em->find(DuelPlayerRecord::class,['duelId'=>$duelId,'userId'=>$userId]);if(!$p instanceof DuelPlayerRecord)throw new \DomainException('Participante não encontrado.');$this->em->createQueryBuilder()->delete(DuelSubjectRecord::class,'s')->where('s.duelId=:d')->andWhere('s.userId=:u')->setParameter('d',$duelId)->setParameter('u',$userId)->getQuery()->execute();foreach(array_values(array_unique($ids)) as $id){$s=new DuelSubjectRecord();$s->duelId=$duelId;$s->userId=$userId;$s->taxonomySubjectId=$id;$this->em->persist($s);}$p->readyAt=$readyAt; }
    public function participantSubjectIds(string $duelId): array { return array_values($this->em->createQueryBuilder()->select('s.taxonomySubjectId')->from(DuelSubjectRecord::class,'s')->where('s.duelId=:d')->setParameter('d',$duelId)->getQuery()->getSingleColumnResult()); }
    public function recentQuestionIdsForParticipants(string $duelId): array { return []; }
    public function freezeQuestions(string $duelId,array $ids): void { if($this->em->getRepository(DuelQuestionRecord::class)->findOneBy(['duelId'=>$duelId]))return;foreach($ids as $i=>$id){$q=new DuelQuestionRecord();$q->duelId=$duelId;$q->position=$i+1;$q->questionId=$id;$this->em->persist($q);} }
    public function appendAnswer(DuelAnswer $a): bool { if($this->em->getRepository(DuelAnswerRecord::class)->findOneBy(['duelId'=>$a->duelId,'position'=>$a->position,'userId'=>$a->userId]))return false;$r=new DuelAnswerRecord();foreach(['id','duelId','position','userId','optionId','receivedAt','elapsedMilliseconds','isCorrect','correctRank','points'] as $p){$r->$p=$a->$p;}$this->em->persist($r);return true; }
    public function participantCount(string $duelId): int { return (int)$this->em->createQueryBuilder()->select('COUNT(p.userId)')->from(DuelPlayerRecord::class,'p')->where('p.duelId=:d')->setParameter('d',$duelId)->getQuery()->getSingleScalarResult(); }
    public function readyParticipantCount(string $duelId): int { return (int)$this->em->createQueryBuilder()->select('COUNT(p.userId)')->from(DuelPlayerRecord::class,'p')->where('p.duelId=:d')->andWhere('p.readyAt IS NOT NULL')->setParameter('d',$duelId)->getQuery()->getSingleScalarResult(); }
    private function listWaiting(array $criteria,int $offset,int $limit): array { $criteria['status']='WAITING'; $records=$this->em->getRepository(DuelRecord::class)->findBy($criteria,['createdAt'=>'DESC'],$limit,$offset); return array_map(fn(DuelRecord $record):ArenaRoomSummary=>new ArenaRoomSummary($record->id,$record->visibility,$record->status,$record->maxPlayers,$this->participantCount($record->id),$record->subjectsPerPlayer,$record->questionCount,$record->questionSeconds,$record->createdAt),$records); }
    private function countWaiting(array $criteria): int { $criteria['status']='WAITING'; return $this->em->getRepository(DuelRecord::class)->count($criteria); }
    private function map(?DuelRecord $r): ?Duel { if(!$r)return null;$ids=array_map(static fn(DuelQuestionRecord $q):string=>$q->questionId,$this->em->getRepository(DuelQuestionRecord::class)->findBy(['duelId'=>$r->id],['position'=>'ASC']));return new Duel($r->id,$r->code,$r->creatorUserId,$r->maxPlayers,$r->subjectsPerPlayer,$r->questionCount,$r->questionSeconds,$r->createdAt,DuelStatus::from($r->status),$r->currentPosition,$r->openedAt,$r->deadlineAt,$r->finishedAt,$ids,DuelVisibility::from($r->visibility)); }
}
