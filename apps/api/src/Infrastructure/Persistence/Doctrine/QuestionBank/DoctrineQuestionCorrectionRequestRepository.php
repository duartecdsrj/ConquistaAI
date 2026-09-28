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
        $this->persistProposedAsset($r,$q);$metadata=$r->proposal["metadata"]??[];if(is_array($metadata)){if(is_string($metadata["board"]??null)&&trim($metadata["board"])!=="")$q->board=mb_substr(trim($metadata["board"]),0,190);if(is_int($metadata["year"]??null)&&$metadata["year"]>=1900&&$metadata["year"]<=2100)$q->examYear=$metadata["year"];$sourceParts=[];if(is_string($metadata["exam"]??null)&&trim($metadata["exam"])!=="")$sourceParts[]="Concurso: ".trim($metadata["exam"]);if(is_string($metadata["position"]??null)&&trim($metadata["position"])!=="")$sourceParts[]="Cargo: ".trim($metadata["position"]);if($sourceParts!==[])$q->source=mb_substr(implode(" | ",$sourceParts),0,255);}$figurePages=array_values(array_unique(array_map(static fn(array $figure):int=>(int)$figure["page"],$r->proposal["figures"])));if($figurePages!==[])$q->sourcePdfPages=$figurePages;$q->statement=(string)$r->proposal["statement"];$q->updatedAt=new \DateTimeImmutable("now",new \DateTimeZone("UTC"));
        foreach($r->proposal['options'] as $option){$record=$this->em->find(QuestionOptionRecord::class,$option['id']);if($record instanceof QuestionOptionRecord)$record->content=(string)$option['content'];}
        $r->status='APPROVED';$r->approvedBy=$adminId;$r->approvedAt=new \DateTimeImmutable('now',new \DateTimeZone('UTC'));$this->em->flush();
    }
    /** @param array<string,mixed> $original @param array<string,mixed> $proposal */
    public function assertProposal(array $original,array $proposal):void {
        if(!is_string($proposal["statement"]??null)||!is_array($proposal["options"]??null)||count($proposal["options"])!==count($original["options"]??[])||!is_array($proposal["figures"]??null)||!is_array($proposal["metadata"]??null))throw new \DomainException("A proposta possui estrutura inválida.");foreach(["board","exam","position"] as $field)if(($proposal["metadata"][$field]??null)!==null&&!is_string($proposal["metadata"][$field]))throw new \DomainException("Os metadados da proposta são inválidos.");if(($proposal["metadata"]["year"]??null)!==null&&!is_int($proposal["metadata"]["year"]))throw new \DomainException("Os metadados da proposta são inválidos.");foreach($proposal["figures"] as $index=>$figure){if(!is_array($figure)||!is_int($figure["page"]??null)||$figure["page"]<1||substr_count($proposal["statement"],"[[FIGURA:".($index+1)."]]")!==1)throw new \DomainException("A proposta possui figuras inválidas.");}$withoutMarkers=preg_replace("/\[\[FIGURA:\d+\]\]/","",$proposal["statement"])??$proposal["statement"];if(preg_match("/\[\[FIGURA:\d+\]\]/",$withoutMarkers)===1)throw new \DomainException("A proposta possui marcador visual inválido.");
        foreach($original['options'] as $i=>$old){$next=$proposal['options'][$i]??null;if(!is_array($next)||($next['id']??null)!==$old['id']||($next['label']??null)!==$old['label']||!is_string($next['content']??null))throw new \DomainException('A proposta tentou alterar a estrutura das alternativas.');}
    }
    private function persistProposedAsset(QuestionCorrectionRequestRecord $request,QuestionRecord $question):void{$figures=$request->proposal["figures"]??[];if($figures===[])return;$sourceJobId=$request->originalSnapshot["sourcePdfJobId"]??null;$sourcePages=(array)($request->originalSnapshot["sourcePdfPages"]??[]);$evidencePages=array_values(array_unique(array_map("intval",(array)($request->proposal["evidence_pages"]??$sourcePages))));if(!is_string($sourceJobId))throw new \DomainException("O PDF de origem não está disponível para anexar a imagem.");$source=$this->em->find(QuestionPdfImportJobRecord::class,$sourceJobId);if(!$source instanceof QuestionPdfImportJobRecord||!is_file($source->documentPath))throw new \DomainException("O PDF de origem não está disponível para anexar a imagem.");$directory="/app/storage/question-pdf-assets/corrections/".$question->id;if(!is_dir($directory)&&!mkdir($directory,0755,true)&&!is_dir($directory))throw new \RuntimeException("Não foi possível preparar o ativo visual.");$order=(int)$this->em->createQueryBuilder()->select("COALESCE(MIN(asset.sortOrder), 0)")->from(QuestionAssetRecord::class,"asset")->where("asset.questionId=:questionId")->setParameter("questionId",$question->id)->getQuery()->getSingleScalarResult()-count($figures);foreach($figures as $index=>$figure){$page=$figure["page"];$preview="/app/storage/question-pdf-assets/correction-previews/".$request->id."-".($index+1).".png";$allowed=in_array($page,$evidencePages,true);if(!$allowed&&!is_file($preview))throw new \DomainException("A página visual proposta não pertence às evidências da questão.");$path=$directory."/correction-".$request->id."-".($index+1)."-page-".$page.".png";if(!is_file($preview)||!copy($preview,$path))throw new \RuntimeException("Não foi possível promover a prévia visual aprovada.");$asset=new QuestionAssetRecord();$asset->id=self::uuid();$asset->questionId=$question->id;$asset->pageNumber=$page;$asset->path=$path;$asset->mimeType="image/png";$asset->sortOrder=$order+$index;$asset->createdAt=new \DateTimeImmutable("now",new \DateTimeZone("UTC"));$this->em->persist($asset);}}
    private static function uuid():string{return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',random_int(0,65535),random_int(0,65535),random_int(0,65535),random_int(16384,20479),random_int(32768,49151),random_int(0,65535),random_int(0,65535),random_int(0,65535));}
}
