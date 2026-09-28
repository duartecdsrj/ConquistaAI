<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionCorrectionRequestRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionAssetRecord;
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
    public function latestCompletedForUser(string $userId): ?QuestionCorrectionRequestRecord {
        $r=$this->em->createQueryBuilder()->select('r')->from(QuestionCorrectionRequestRecord::class,'r')->where('r.requestedBy=:user')->andWhere('r.status IN (:statuses)')->setParameter('user',$userId)->setParameter('statuses',['PROPOSED','FAILED','APPROVED'])->orderBy('r.finishedAt','DESC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
        return $r instanceof QuestionCorrectionRequestRecord?$r:null;
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
        $this->persistProposedAsset($r,$q);$q->statement=(string)$r->proposal['statement'];$q->updatedAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));
        foreach($r->proposal['options'] as $option){$record=$this->em->find(QuestionOptionRecord::class,$option['id']);if($record instanceof QuestionOptionRecord)$record->content=(string)$option['content'];}
        $r->status='APPROVED';$r->approvedBy=$adminId;$r->approvedAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));$this->em->flush();
    }
    /** @param array<string,mixed> $original @param array<string,mixed> $proposal */
    public function assertProposal(array $original,array $proposal):void {
        if(!is_string($proposal["statement"]??null)||!is_array($proposal["options"]??null)||count($proposal["options"])!==count($original["options"]??[])||(array_key_exists("asset_page",$proposal)&&$proposal["asset_page"]!==null&&!is_int($proposal["asset_page"])))throw new \DomainException("A proposta possui estrutura inválida.");
        foreach($original['options'] as $i=>$old){$next=$proposal['options'][$i]??null;if(!is_array($next)||($next['id']??null)!==$old['id']||($next['label']??null)!==$old['label']||!is_string($next['content']??null))throw new \DomainException('A proposta tentou alterar a estrutura das alternativas.');}
    }
    private function persistProposedAsset(QuestionCorrectionRequestRecord $request,QuestionRecord $question):void{$page=$request->proposal["asset_page"]??null;if($page===null)return;if(!is_int($page)||$page<1)throw new \DomainException("A página visual proposta é inválida.");$sourceJobId=$request->originalSnapshot["sourcePdfJobId"]??null;$sourcePages=(array)($request->originalSnapshot["sourcePdfPages"]??[]);$allowed=false;foreach($sourcePages as $sourcePage)if($page>=(int)$sourcePage-2&&$page<=(int)$sourcePage+2)$allowed=true;if(!is_string($sourceJobId)||!$allowed)throw new \DomainException("A página visual proposta não pertence às evidências da questão.");$source=$this->em->find(QuestionPdfImportJobRecord::class,$sourceJobId);if(!$source instanceof QuestionPdfImportJobRecord||!is_file($source->documentPath))throw new \DomainException("O PDF de origem não está disponível para anexar a imagem.");$directory="/app/storage/question-pdf-assets/corrections/".$question->id;if(!is_dir($directory)&&!mkdir($directory,0755,true)&&!is_dir($directory))throw new \RuntimeException("Não foi possível preparar o ativo visual.");$path=$directory."/correction-".$request->id."-page-".$page.".png";$preview="/app/storage/question-pdf-assets/correction-previews/".$request->id.".png";if(!is_file($preview)||!copy($preview,$path))throw new \RuntimeException("Não foi possível promover a prévia visual aprovada.");$order=(int)$this->em->createQueryBuilder()->select("COALESCE(MAX(asset.sortOrder), 0)")->from(QuestionAssetRecord::class,"asset")->where("asset.questionId=:questionId")->setParameter("questionId",$question->id)->getQuery()->getSingleScalarResult()+1;$asset=new QuestionAssetRecord();$asset->id=self::uuid();$asset->questionId=$question->id;$asset->pageNumber=$page;$asset->path=$path;$asset->mimeType="image/png";$asset->sortOrder=$order;$asset->createdAt=new \DateTimeImmutable("now",new \DateTimeZone("UTC"));$this->em->persist($asset);}
    private static function uuid():string{return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',random_int(0,65535),random_int(0,65535),random_int(0,65535),random_int(16384,20479),random_int(32768,49151),random_int(0,65535),random_int(0,65535),random_int(0,65535));}
}
