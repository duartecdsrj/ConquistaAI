<?php
declare(strict_types=1);
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionCorrectionRequestRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionPdfImportJobRecord;
require __DIR__.'/../vendor/autoload.php';
$em=DoctrineEntityManagerFactory::create();
$requests=new DoctrineQuestionCorrectionRequestRepository($em);
$job=$requests->claimNext();
if($job===null){fwrite(STDOUT,"No correction request pending\n");exit(0);}
$workspace=sys_get_temp_dir().'/question-correction-'.$job->id;
if(!mkdir($workspace,0700,true)&&!is_dir($workspace))throw new RuntimeException('Não foi possível preparar o ambiente isolado.');
$images=[];
try {
    $schema=['type'=>'object','additionalProperties'=>false,'required'=>['statement','options','summary'],'properties'=>['statement'=>['type'=>'string'],'options'=>['type'=>'array','items'=>['type'=>'object','additionalProperties'=>false,'required'=>['id','label','content'],'properties'=>['id'=>['type'=>'string'],'label'=>['type'=>'string'],'content'=>['type'=>'string']]]],'summary'=>['type'=>'string']]];
    file_put_contents($workspace.'/response-schema.json',json_encode($schema,JSON_THROW_ON_ERROR));
    file_put_contents($workspace.'/question.json',json_encode(['question'=>$job->originalSnapshot,'instruction'=>$job->instruction],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR));
    $sourceJobId=$job->originalSnapshot['sourcePdfJobId']??null;$pages=$job->originalSnapshot['sourcePdfPages']??[];
    if(is_string($sourceJobId)&&is_array($pages)){
        $source=$em->find(QuestionPdfImportJobRecord::class,$sourceJobId);
        if($source instanceof QuestionPdfImportJobRecord&&is_file($source->documentPath))foreach(array_values(array_unique(array_filter(array_map('intval',$pages),static fn(int $page):bool=>$page>0))) as $page){
            $target=$workspace.'/source-page-'.$page;
            exec('pdftoppm -png -f '.$page.' -l '.$page.' -singlefile '.escapeshellarg($source->documentPath).' '.escapeshellarg($target),$ignored,$status);
            if($status===0&&is_file($target.'.png'))$images[]=$target.'.png';
        }
    }
    $prompt='Leia exclusivamente question.json e, quando presentes, as imagens source-page-*.png, que são as páginas do PDF de origem desta única questão. Faça somente correção estrutural ou de formatação. Nunca reescreva intelectualmente, complete, corrija tecnicamente, revele ou altere gabarito, altere IDs, rótulos, quantidade ou ordem de alternativas. Não invente imagem, texto, dado, código ou resposta. Se a evidência não for suficiente, preserve o conteúdo. Devolva somente JSON conforme response-schema.json; summary explica alterações estruturais e incertezas.';
    $command=['codex','exec','--ephemeral','--ignore-user-config','--ignore-rules','--sandbox','read-only','--skip-git-repo-check','--cd',$workspace];
    foreach($images as $image){$command[]='-i';$command[]=$image;}
    array_push($command,'--output-schema',$workspace.'/response-schema.json','--output-last-message',$workspace.'/proposal.json',$prompt);
    $process=proc_open(implode(' ',array_map('escapeshellarg',$command)),[1=>['pipe','w'],2=>['pipe','w']],$pipes,$workspace,['CODEX_API_KEY'=>(string)getenv('CODEX_API_KEY'),'HOME'=>$workspace,'PATH'=>(string)getenv('PATH')]);
    if(!is_resource($process))throw new RuntimeException('Não foi possível iniciar Codex.');
    stream_get_contents($pipes[1]);$stderr=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);
    if(proc_close($process)!==0)throw new RuntimeException('Codex não concluiu a proposta: '.mb_substr($stderr,0,240));
    $proposal=json_decode((string)file_get_contents($workspace.'/proposal.json'),true,512,JSON_THROW_ON_ERROR);
    if(!is_array($proposal))throw new RuntimeException('Codex não retornou uma proposta válida.');
    $requests->assertProposal($job->originalSnapshot,$proposal);$requests->propose($job,$proposal);
    fwrite(STDOUT,"Proposed correction {$job->id}\n");
} catch(Throwable $e) {$requests->fail($job,'Não foi possível gerar uma proposta segura para esta questão.');fwrite(STDERR,$e->getMessage()."\n");exit(1);
} finally {foreach(array_merge(['proposal.json','response-schema.json','question.json'],array_map('basename',$images)) as $file){if(is_file($workspace.'/'.$file))unlink($workspace.'/'.$file);}rmdir($workspace);}
