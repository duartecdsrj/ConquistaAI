<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use App\Domain\Taxonomy\Entity\TaxonomySubject;
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
$governance = ensureGovernanceParent($taxonomy);
$writer = new DoctrineQuestionPdfQuestionWriter($em, new DoctrineQuestionDuplicateDetector($em), $taxonomy, new DoctrineQuestionTaxonomyAssignmentRepository($em), new SubjectTaxonomyService(), new ImportedQuestionContentSanitizer());
$pages = (new PdftotextPdfTextExtractor())->extractPages($job->documentPath);
$starts=[]; $text=''; foreach($pages as $index=>$page){$starts[]=['offset'=>strlen($text),'page'=>$index+1];$text.="\n".$page;}
$header='(?:FGV|FCC|CESGRANRIO|VUNESP|QUADRIX|IBFC|AOCP|CONSULPLAN|CEBRASPE|CESPE|FUNDATEC|IADES|COPEVE|COMPERVE|ESAF)';
$blocks=preg_split('/(?=^\h*\d{1,3}\.\h+\(?'.$header.')/mu',$text,-1,PREG_SPLIT_NO_EMPTY) ?: [];
$matches=[];
foreach($blocks as $block){
  if(!preg_match('/^\h*(?<number>\d{1,3})\.\h+(?<meta>[^\n]+)\n(?<body>[\s\S]*)$/u',$block,$head))continue;
  if(!preg_match('/^(?<statement>[\s\S]{30,}?)\n\h*[Aa]\)\h*(?<a>[\s\S]*?)\n\h*[Bb]\)\h*(?<b>[\s\S]*?)\n\h*[Cc]\)\h*(?<c>[\s\S]*?)\n\h*[Dd]\)\h*(?<d>[\s\S]*?)\n\h*[Ee]\)\h*(?<e>[\s\S]*?)(?=\n\h*Comentários:)/u',$head['body'],$parts))continue;
  $offset=strpos($text,$block); $matches[]=['meta'=>[$head['meta'],$offset===false?0:$offset],'statement'=>[$parts['statement'],$offset===false?0:$offset],'a'=>[$parts['a'],0],'b'=>[$parts['b'],0],'c'=>[$parts['c'],0],'d'=>[$parts['d'],0],'e'=>[$parts['e'],0],0=>[$block,$offset===false?0:$offset],'_text'=>$block,'_starts'=>$starts,'number'=>(int)$head['number']];
}
$questions=[];
foreach($matches as $match){
  $opening=''; if(preg_match('/^\((?<identity>[^)]+)\)\s*(?<opening>.*)$/u', $match['meta'][0], $identityLine)){ $match['meta'][0]=$identityLine['identity']; $opening=$identityLine['opening']; } $statement=cleanPdf($opening."\n".$match['statement'][0]); $options=[];
  foreach(['a','b','c','d','e'] as $label)$options[]=['content'=>cleanPdf($match[$label][0])];
  if(mb_strlen($statement)<35||count($options)!==5)continue;
  $meta=preg_replace('/\s+/u',' ',trim($match['meta'][0]))??'';
  preg_match('/^(?<board>[A-ZÀ-Ú\/\- ]+?)\s*(?:-|–)\s*(?<year>20\d{2})\s*(?:-|–)?\s*(?<source>.*)$/u',$meta,$identity);
  $offset=$match[0][1];$page=1;foreach($starts as $start){if($start['offset']>$offset)break;$page=$start['page'];}
  $content=mb_strtolower($statement.' '.implode(' ',array_column($options,'content')));
  [$path,$parent]=placement($content);
  $questions[]=['type'=>'MULTIPLE_CHOICE','statement'=>$statement,'options'=>$options,'correct_option'=>(answerAfter($text,$offset,mb_strlen($match[0][0])) ?? governanceAnswer((int)($match['number'] ?? 0))),'answer_key_source'=>'OFFICIAL','board'=>trim($identity['board']??$meta)?:null,'exam'=>trim($identity['source']??'')?:null,'year'=>isset($identity['year'])?(int)$identity['year']:null,'taxonomy_path'=>$path,'parent_subject'=>$parent,'pages'=>[$page],'difficulty'=>mb_strlen($statement)>900?'HARD':(mb_strlen($statement)>420?'MEDIUM':'EASY')];
}
if($dryRun){echo json_encode(['matches'=>count($matches),'candidates'=>count($questions),'sample'=>array_slice($questions,0,3)],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).PHP_EOL;exit;}
$result=['created'=>0,'duplicates'=>0,'classified'=>0,'failed'=>0,'createdSubjects'=>0];foreach($questions as $question){$partial=$writer->write($job->createdBy,[$question],[],$job->id);foreach($result as $key=>$value)$result[$key]+=$partial[$key];}
$job->status='COMPLETED';$job->progress=100;$job->pageCount=count($pages);$job->candidatePages=count($pages);$job->extractedQuestions=count($questions);$job->classifiedQuestions=$result['classified'];$job->createdQuestions=$result['created'];$job->duplicateQuestions=$result['duplicates'];$job->failedQuestions=$result['failed'];$job->createdTaxonomySubjects=$result['createdSubjects'];$job->errorMessage='Extração direta determinística; apenas questões objetivas multibanca completas.';$job->finishedAt=new DateTimeImmutable('now');$em->flush();echo json_encode(['extracted'=>count($questions),'result'=>$result],JSON_UNESCAPED_UNICODE).PHP_EOL;
function ensureGovernanceParent(DoctrineTaxonomySubjectRepository $taxonomy): TaxonomySubject { $parent=$taxonomy->findBySlug('governanca-de-ti'); if($parent instanceof TaxonomySubject)return $parent; $root=$taxonomy->findBySlug('tecnologia-da-informacao'); if(!$root instanceof TaxonomySubject)throw new RuntimeException('Raiz de TI não encontrada.'); $parent=new TaxonomySubject(bin2hex(random_bytes(16)), $root->id, 'Governança de TI', 'governanca-de-ti', null, $root->level+1, true); $taxonomy->save($parent); return $parent; }
function cleanPdf(string $value): string { $value=preg_replace('/^.*(?:Concursos da Área Fiscal|www\.estrategiaconcursos\.com\.br|Eletronica Em Arte|André Castro, Equipe Informática e TI|Equipe Informática e TI, Fernando Pedrosa Lopes , Paolla Ramos|Aula \d{2}).*$/mu', '', $value)??$value; return trim((string)preg_replace('/\n{3,}/u', "\n\n", $value)); }
function placement(string $content):array { if(preg_match('/\b(?:pmbok|projeto|cronograma|escopo|stakeholder)/u',$content))return [['Tecnologia da Informação','Gestão de Projetos','PMBOK'],'Gestão de Projetos']; if(preg_match('/\b(?:cobit|governan)/u',$content))return [['Tecnologia da Informação','Governança de TI','COBIT'],'Governança de TI']; if(preg_match('/\b(?:itil|gestão de servi|service value)/u',$content))return [['Tecnologia da Informação','Governança de TI','ITIL'],'Governança de TI']; if(preg_match('/\b(?:cmmi|mps\.br|maturidade)/u',$content))return [['Tecnologia da Informação','Governança de TI','Modelos de Maturidade'],'Governança de TI']; if(preg_match('/\b(?:peti|planejamento estrat)/u',$content))return [['Tecnologia da Informação','Governança de TI','Planejamento Estratégico de TI'],'Governança de TI']; if(preg_match('/\b(?:iso\s*(?:20000|38500)|governança corporativa)/u',$content))return [['Tecnologia da Informação','Governança de TI','Normas de Governança de TI'],'Governança de TI']; if(preg_match('/\b(?:ssl|tls|https|certificad)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','SSL e TLS'],'Segurança da Informação']; if(preg_match('/\b(?:ipsec|vpn|tor|deep.?web)/u',$content))return [['Tecnologia da Informação','Redes de Computadores','Protocolos','IPsec'],'Protocolos']; if(preg_match('/\b(?:raid|nas|san|armazenamento|storage)/u',$content))return [['Tecnologia da Informação','Infraestrutura','Storage NAS e SAN'],'Infraestrutura']; if(preg_match('/\b(?:qos|qualidade de servi)/u',$content))return [['Tecnologia da Informação','Redes de Computadores','Qualidade de Serviço (QoS)'],'Redes de Computadores']; if(preg_match('/\b(?:ldap|active directory|x\.500)/u',$content))return [['Tecnologia da Informação','Sistemas Operacionais','Active Directory'],'Sistemas Operacionais']; if(preg_match('/\b(?:nuvem|cloud)/u',$content))return [['Tecnologia da Informação','Infraestrutura','Computação em Nuvem'],'Infraestrutura']; if(preg_match('/\b(?:criptograf|hash|cifra|chave.p.blica|certifica)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','Criptografia e Certificação Digital'],'Segurança da Informação']; if(preg_match('/\b(?:firewall|proxy|filtro de pacote|conexão estabelecida|stateful)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','Firewall e Proxy'],'Segurança da Informação']; if(preg_match('/\b(?:ids|ips|intrusão)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','IDS e IPS'],'Segurança da Informação']; if(preg_match('/\b(?:malware|ransomware|ataque|vulnerab)/u',$content))return [['Tecnologia da Informação','Segurança da Informação','Gestão de Vulnerabilidades'],'Segurança da Informação']; return [['Tecnologia da Informação','Governança de TI','Fundamentos de Governança de TI'],'Governança de TI']; }
function governanceAnswer(int $number): ?string { return [21=>'D',22=>'C',23=>'E',24=>'E',25=>'A',26=>'C',45=>'D',46=>'A',47=>'C',60=>'D',61=>'A',62=>'A',63=>'D',64=>'B',65=>'A',66=>'C',91=>'D',92=>'A',93=>'A',94=>'D',95=>'D',96=>'D',97=>'E',98=>'B',99=>'B',100=>'A'][$number] ?? null; }
function answerAfter(string $text,int $offset,int $length):?string{$tail=mb_substr($text,$offset,$length);return preg_match('/Gabarito:\s*(?:Letra\s*)?([A-E])\b/iu',$tail,$m)?$m[1]:null;}
