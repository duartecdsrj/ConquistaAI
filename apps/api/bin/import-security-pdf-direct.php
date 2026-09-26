<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
use App\Infrastructure\Extraction\PdftotextPdfTextExtractor;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionDuplicateDetector;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionPdfQuestionWriter;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionTaxonomyAssignmentRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionPdfImportJobRecord;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\DoctrineTaxonomySubjectRepository;

$jobId = $argv[1] ?? '';
$dryRun = in_array('--dry-run', $argv, true);
$em = DoctrineEntityManagerFactory::create();
$job = $em->find(QuestionPdfImportJobRecord::class, $jobId);
if (!$job instanceof QuestionPdfImportJobRecord) throw new RuntimeException('Job não encontrado.');
$taxonomy = new DoctrineTaxonomySubjectRepository($em);
$writer = new DoctrineQuestionPdfQuestionWriter($em, new DoctrineQuestionDuplicateDetector($em), $taxonomy, new DoctrineQuestionTaxonomyAssignmentRepository($em), new SubjectTaxonomyService(), new ImportedQuestionContentSanitizer());
$pages = (new PdftotextPdfTextExtractor())->extractPages($job->documentPath);
$pattern='/^\s*\d{1,3}\.\s+(?<meta>(?:FGV|FCC|CESGRANRIO|VUNESP|QUADRIX|IBFC|AOCP|CONSULPLAN|CEBRASPE|CESPE|FUNDATEC|IADES|COPEVE|COMPERVE|ESAF)[^\n]{5,500})\n(?<statement>.{30,7000}?)\n\s*A[)\.\s]+(?<a>.{1,2200}?)\n\s*B[)\.\s]+(?<b>.{1,2200}?)\n\s*C[)\.\s]+(?<c>.{1,2200}?)\n\s*D[)\.\s]+(?<d>.{1,2200}?)\n\s*E[)\.\s]+(?<e>.{1,2200}?)(?=\n\s*Comentários:)/msu';
$matches=[];
foreach (array_chunk($pages, 3, true) as $chunk) {
  $chunkStarts=[]; $chunkText=''; foreach($chunk as $index=>$page){$chunkStarts[]=['offset'=>strlen($chunkText),'page'=>$index+1];$chunkText.="\n".$page;}
  preg_match_all($pattern, $chunkText, $found, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
  foreach($found as $match){$match['_text']=$chunkText;$match['_starts']=$chunkStarts;$matches[]=$match;}
}
$questions=[];
foreach($matches as $match){
  $statement=cleanPdf($match['statement'][0]); $options=[];
  foreach(['a','b','c','d','e'] as $label)$options[]=['content'=>cleanPdf($match[$label][0])];
  if(mb_strlen($statement)<35||count($options)!==5)continue;
  $meta=preg_replace('/\s+/u',' ',trim($match['meta'][0]))??'';
  preg_match('/^(?<board>[A-ZÀ-Ú\/\- ]+?)\s*(?:-|–)\s*(?<year>20\d{2})\s*(?:-|–)?\s*(?<source>.*)$/u',$meta,$identity);
  $offset=$match[0][1];$page=1;foreach($match['_starts'] as $start){if($start['offset']>$offset)break;$page=$start['page'];}
  $content=mb_strtolower($statement.' '.implode(' ',array_column($options,'content')));
  [$path,$parent]=placement($content);
  $questions[]=['type'=>'MULTIPLE_CHOICE','statement'=>$statement,'options'=>$options,'correct_option'=>answerAfter($match['_text'],$offset,mb_strlen($match[0][0])),'answer_key_source'=>'OFFICIAL','board'=>trim($identity['board']??$meta)?:null,'exam'=>trim($identity['source']??'')?:null,'year'=>isset($identity['year'])?(int)$identity['year']:null,'taxonomy_path'=>$path,'parent_subject'=>$parent,'pages'=>[$page],'difficulty'=>mb_strlen($statement)>900?'HARD':(mb_strlen($statement)>420?'MEDIUM':'EASY')];
}
if($dryRun){echo json_encode(['matches'=>count($matches),'candidates'=>count($questions),'sample'=>array_slice($questions,0,3)],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).PHP_EOL;exit;}
$result=['created'=>0,'duplicates'=>0,'classified'=>0,'failed'=>0,'createdSubjects'=>0];foreach($questions as $question){$partial=$writer->write($job->createdBy,[$question],[],$job->id);foreach($result as $key=>$value)$result[$key]+=$partial[$key];}
$job->status='COMPLETED';$job->progress=100;$job->pageCount=count($pages);$job->candidatePages=count($pages);$job->extractedQuestions=count($questions);$job->classifiedQuestions=$result['classified'];$job->createdQuestions=$result['created'];$job->duplicateQuestions=$result['duplicates'];$job->failedQuestions=$result['failed'];$job->createdTaxonomySubjects=$result['createdSubjects'];$job->errorMessage='Extração direta determinística; apenas questões objetivas multibanca completas.';$job->finishedAt=new DateTimeImmutable('now');$em->flush();echo json_encode(['extracted'=>count($questions),'result'=>$result],JSON_UNESCAPED_UNICODE).PHP_EOL;
function cleanPdf(string $value): string { $value=preg_replace('/^.*(?:Concursos da Área Fiscal|www\.estrategiaconcursos\.com\.br|Eletronica Em Arte|André Castro, Equipe Informática e TI|Aula \d{2}).*$/mu', '', $value)??$value; return trim((string)preg_replace('/\n{3,}/u', "\n\n", $value)); }
function placement(string $content):array { if(preg_match('/\b(?:ssl|tls|https|certificad)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','SSL e TLS'],'Segurança da Informação']; if(preg_match('/\b(?:ipsec|vpn|tor|deep.?web)/u',$content))return [['Tecnologia da Informação','Redes de Computadores','Protocolos','IPsec'],'Protocolos']; if(preg_match('/\b(?:raid|nas|san|armazenamento|storage)/u',$content))return [['Tecnologia da Informação','Infraestrutura','Storage NAS e SAN'],'Infraestrutura']; if(preg_match('/\b(?:qos|qualidade de servi)/u',$content))return [['Tecnologia da Informação','Redes de Computadores','Qualidade de Serviço (QoS)'],'Redes de Computadores']; if(preg_match('/\b(?:ldap|active directory|x\.500)/u',$content))return [['Tecnologia da Informação','Sistemas Operacionais','Active Directory'],'Sistemas Operacionais']; if(preg_match('/\b(?:nuvem|cloud)/u',$content))return [['Tecnologia da Informação','Infraestrutura','Computação em Nuvem'],'Infraestrutura']; if(preg_match('/\b(?:criptograf|hash|cifra|chave.p.blica|certifica)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','Criptografia e Certificação Digital'],'Segurança da Informação']; if(preg_match('/\b(?:firewall|proxy|filtro de pacote|conexão estabelecida|stateful)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','Firewall e Proxy'],'Segurança da Informação']; if(preg_match('/\b(?:ids|ips|intrusão)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','IDS e IPS'],'Segurança da Informação']; if(preg_match('/\b(?:malware|ransomware|ataque|vulnerab)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','Gestão de Vulnerabilidades'],'Segurança da Informação']; return [['Tecnologia da Informação','Segurança da Informação','Fundamentos de Segurança da Informação'],'Segurança da Informação']; }
function answerAfter(string $text,int $offset,int $length):?string{$tail=mb_substr($text,$offset,$length+10000);return preg_match('/Gabarito:\s*([A-E])\b/u',$tail,$m)?$m[1]:null;}
