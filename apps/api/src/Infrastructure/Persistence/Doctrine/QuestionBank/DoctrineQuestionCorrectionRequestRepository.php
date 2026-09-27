<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionCorrectionRequestRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionPdfImportJobRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineQuestionCorrectionRequestRepository {
    public function __construct(private readonly EntityManagerInterface $em) {}
    /** @return array<string,mixed>|null */
    public function questionSnapshot(string $questionId): ?array {
        $q=$this->em->find(QuestionRecord::class,$questionId);
        if(!$q instanceof QuestionRecord || $q->status!=='PUBLISHED') return null;
        $options=$this->em->createQueryBuilder()->select('o')->from(QuestionOptionRecord::class,'o')->where('o.questionId=:id')->setParameter('id',$q->id)->orderBy('o.sortOrder','ASC')->getQuery()->getResult();
        return ['id'=>$q->id,'statement'=>$q->statement,'options'=>array_map(static fn(QuestionOptionRecord $o):array=>['id'=>$o->id,'label'=>$o->label,'content'=>$o->content,'position'=>$o->sortOrder],$options),'sourcePdfJobId'=>$q->sourcePdfJobId,'sourcePdfPages'=>$q->sourcePdfPages??[]];
    }
    public function create(string $questionId,string $userId,string $instruction,array $snapshot): QuestionCorrectionRequestRecord {
        $r=new QuestionCorrectionRequestRecord();$r->id=self::uuid();$r->questionId=$questionId;$r->requestedBy=$userId;$r->instruction=$instruction;$r->status='PENDING';$r->originalSnapshot=$snapshot;$r->createdAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));$this->em->persist($r);$this->em->flush();return $r;
    }
    public function latestVisible(string $questionId,string $userId,bool $admin): ?QuestionCorrectionRequestRecord {
        $q=$this->em->createQueryBuilder()->select('r')->from(QuestionCorrectionRequestRecord::class,'r')->where('r.questionId=:question')->setParameter('question',$questionId)->orderBy('r.createdAt','DESC')->setMaxResults(1);if(!$admin)$q->andWhere('r.requestedBy=:user')->setParameter('user',$userId);$r=$q->getQuery()->getOneOrNullResult();return $r instanceof QuestionCorrectionRequestRecord?$r:null;
    }
    public function claimNext(): ?QuestionCorrectionRequestRecord {
        $r=$this->em->createQueryBuilder()->select('r')->from(QuestionCorrectionRequestRecord::class,'r')->where('r.status=:status')->setParameter('status','PENDING')->orderBy('r.createdAt','ASC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if(!$r instanceof QuestionCorrectionRequestRecord)return null;$r->status='PROCESSING';$r->startedAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));$this->em->flush();return $r;
    }
    public function propose(QuestionCorrectionRequestRecord $r,array $proposal):void{$r->proposal=$proposal;$r->status='PROPOSED';$r->finishedAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));$this->em->flush();}
    public function fail(QuestionCorrectionRequestRecord $r,string $error):void{$r->status='FAILED';$r->errorMessage=mb_substr($error,0,500);$r->finishedAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));$this->em->flush();}
    public function find(string $id):?QuestionCorrectionRequestRecord{$r=$this->em->find(QuestionCorrectionRequestRecord::class,$id);return $r instanceof QuestionCorrectionRequestRecord?$r:null;}
    public function approve(QuestionCorrectionRequestRecord $r,string $adminId):void {
        if($r->status!=='PROPOSED'||!is_array($r->proposal))throw new \DomainException('A proposta não está disponível para aprovação.');
        $this->assertProposal($r->originalSnapshot,$r->proposal);$q=$this->em->find(QuestionRecord::class,$r->questionId);if(!$q instanceof QuestionRecord)throw new \DomainException('Questão não encontrada.');
        $q->statement=(string)$r->proposal['statement'];$q->updatedAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));
        foreach($r->proposal['options'] as $option){$record=$this->em->find(QuestionOptionRecord::class,$option['id']);if($record instanceof QuestionOptionRecord)$record->content=(string)$option['content'];}
        $r->status='APPROVED';$r->approvedBy=$adminId;$r->approvedAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));$this->em->flush();
    }
    /** @param array<string,mixed> $original @param array<string,mixed> $proposal */
    public function assertProposal(array $original,array $proposal):void {
        if(!is_string($proposal['statement']??null)||!is_array($proposal['options']??null)||count($proposal['options'])!==count($original['options']??[]))throw new \DomainException('A proposta possui estrutura inválida.');
        foreach($original['options'] as $i=>$old){$next=$proposal['options'][$i]??null;if(!is_array($next)||($next['id']??null)!==$old['id']||($next['label']??null)!==$old['label']||!is_string($next['content']??null))throw new \DomainException('A proposta tentou alterar a estrutura das alternativas.');}
    }
    private static function uuid():string{return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',random_int(0,65535),random_int(0,65535),random_int(0,65535),random_int(16384,20479),random_int(32768,49151),random_int(0,65535),random_int(0,65535),random_int(0,65535));}
}
