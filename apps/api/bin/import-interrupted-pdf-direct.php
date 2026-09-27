<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\QuestionBank\Service\ImportedQuestionContentSanitizer;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
use App\Infrastructure\Extraction\PdftotextPdfTextExtractor;
use App\Infrastructure\Extraction\PdfimagesQuestionPdfAssetExtractor;
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
$writer = new DoctrineQuestionPdfQuestionWriter(
    $em,
    new DoctrineQuestionDuplicateDetector($em),
    $taxonomy,
    new DoctrineQuestionTaxonomyAssignmentRepository($em),
    new SubjectTaxonomyService(),
    new ImportedQuestionContentSanitizer(),
);
$pages = (new PdftotextPdfTextExtractor())->extractPages($job->documentPath);
$pageAssets = (new PdfimagesQuestionPdfAssetExtractor())->extract($job->documentPath, $job->documentSha256);
$starts = []; $document = '';
foreach ($pages as $index => $page) { $starts[] = ['offset' => strlen($document), 'page' => $index + 1]; $document .= "\n" . $page; }

$header = '/^\\h*(?<number>\\d{1,3})[.\\-]\\p{Cf}*\\h+\\((?<meta>[^\\n]{8,900})\\)\\h*(?<body>[\\s\\S]*?)(?=^\\h*\\d{1,3}[.\\-]\\p{Cf}*\\h+\\(|\\z)/mu';
preg_match_all($header, $document, $blocks, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
$questions = [];
foreach ($blocks as $block) {
    $body = $block['body'][0];
    $optionsPattern = '/^(?<statement>[\\s\\S]{30,}?)\\n\\h*[Aa][)\\.]\\h+(?<a>[\\s\\S]*?)\\n\\h*[Bb][)\\.]\\h+(?<b>[\\s\\S]*?)\\n\\h*[Cc][)\\.]\\h+(?<c>[\\s\\S]*?)\\n\\h*[Dd][)\\.]\\h+(?<d>[\\s\\S]*?)\\n\\h*[Ee][)\\.]\\h+(?<e>[\\s\\S]*?)(?=\\n\\h*(?:Resolu[cç][aã]o:|Coment[aá]rios?:|Gabarito:|$))/msu';
    if (preg_match($optionsPattern, $body, $parts) !== 1) continue;
    $statement = clean($parts['statement']);
    $options = array_map(static fn (string $key): array => ['content' => clean($parts[$key])], ['a', 'b', 'c', 'd', 'e']);
    if (mb_strlen($statement) < 35 || array_filter($options, static fn (array $option): bool => $option['content'] === '')) continue;
    $meta = clean($block['meta'][0]);
    $board = board($meta);
    $year = preg_match('/\\b(20\\d{2})\\b/u', $meta, $yearMatch) === 1 ? (int) $yearMatch[1] : null;
    $offset = $block[0][1];
    $page = pageForOffset($starts, $offset);
    $content = mb_strtolower($statement . ' ' . implode(' ', array_column($options, 'content')));
    [$path, $parent] = placement($job->documentOriginalName, $content);
    $answer = preg_match('/\\bGabarito:\\h*(?:Letra\\h*)?([A-E])\\b/iu', $block[0][0], $answerMatch) === 1 ? strtoupper($answerMatch[1]) : null;
    $imagePages = hasVisualReference($statement . ' ' . implode(' ', array_column($options, 'content'))) ? [$page] : [];
    $questions[] = [
        'type' => 'MULTIPLE_CHOICE', 'statement' => $statement, 'options' => $options,
        'correct_option' => $answer, 'answer_key_source' => $answer === null ? null : 'OFFICIAL',
        'board' => $board, 'exam' => $meta, 'year' => $year,
        'taxonomy_path' => $path, 'parent_subject' => $parent, 'pages' => [$page], 'image_pages' => $imagePages,
        'difficulty' => mb_strlen($statement) > 900 ? 'HARD' : (mb_strlen($statement) > 420 ? 'MEDIUM' : 'EASY'),
    ];
}
if ($dryRun) { echo json_encode(['blocks' => count($blocks), 'candidates' => count($questions), 'with_official_answer' => count(array_filter($questions, static fn (array $question): bool => $question['correct_option'] !== null)), 'sample' => array_slice($questions, 0, 2)], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL; exit; }
$result = ['created' => 0, 'duplicates' => 0, 'classified' => 0, 'failed' => 0, 'createdSubjects' => 0];
foreach ($questions as $question) { $partial = $writer->write($job->createdBy, [$question], $pageAssets, $job->id); foreach ($result as $key => $value) $result[$key] += $partial[$key]; }
$job->status = 'COMPLETED'; $job->progress = 100; $job->pageCount = count($pages); $job->candidatePages = count($pages); $job->processedChunks = count($pages);
$job->extractedQuestions = count($questions); $job->classifiedQuestions = $result['classified']; $job->createdQuestions = $result['created']; $job->duplicateQuestions = $result['duplicates']; $job->failedQuestions = $result['failed']; $job->createdTaxonomySubjects = $result['createdSubjects'];
$job->errorMessage = 'Extração direta determinística sem provedor: apenas questões objetivas completas; itens de certo/errado e discursivos foram descartados.'; $job->finishedAt = new DateTimeImmutable('now');
$em->flush();
echo json_encode(['extracted' => count($questions), 'result' => $result], JSON_UNESCAPED_UNICODE) . PHP_EOL;

function clean(string $value): string { $value = preg_replace('/^.*(?:www\\.estrategiaconcursos\\.com\\.br|Eletronica Em Arte|TI TOTAL para|Professor [^\\n]+|Aula \\d+|Licensed to ).*$/mu', '', $value) ?? $value; return trim((string) preg_replace('/\\n{3,}/u', "\\n\\n", $value)); }
function board(string $meta): ?string { return preg_match('/\\b(FGV|FCC|CESGRANRIO|VUNESP|QUADRIX|IBFC|AOCP|CONSULPLAN|CEBRASPE|CESPE|FUNDATEC|IADES|COPEVE|COMPERVE|ESAF)\\b/iu', $meta, $match) === 1 ? strtoupper($match[1]) : null; }
function pageForOffset(array $starts, int $offset): int { $page = 1; foreach ($starts as $start) { if ($start['offset'] > $offset) break; $page = $start['page']; } return $page; }
function hasVisualReference(string $value): bool { return preg_match('/\\b(?:figura|imagem|gr[aá]fico|tabela|quadro|diagrama|esquema|ilustra[cç][aã]o|mapa|fluxograma)\\b/iu', $value) === 1; }
function placement(string $name, string $content): array
{
    $root = 'Tecnologia da Informação';
    $infrastructure = 'Infraestrutura';
    $operatingSystems = 'Sistemas Operacionais';
    $security = 'Segurança da Informação';
    $networks = 'Redes de Computadores';
    $protocols = 'Protocolos';

    if (preg_match('/\b(?:ssl|tls|https)\b/u', $content)) return [[$root, $security, 'SSL e TLS'], $security];
    if (preg_match('/\b(?:ipsec|vpn|openvpn|wireguard|tor|deepweb)\b/u', $content)) return [[$root, $security, 'Segurança de Redes'], $security];
    if (preg_match('/\b(?:criptografia|criptogr[aá]f|aes|rsa|diffie|hash|sha-?d*|md5|esteganografia)\b/u', $content)) return [[$root, $security, 'Criptografia e Certificação Digital'], $security];
    if (preg_match('/\b(?:assinatura digital|certifica[cç][aã]o digital|pki|icp[- ]?brasil|autoridade certificadora|crl)\b/u', $content)) return [[$root, $security, 'Criptografia e Certificação Digital'], $security];
    if (preg_match('/\b(?:oauth|jwt|identidade digital|controle de acesso|gest[aã]o de identidade|iam|mfa|sso|autentica[cç][aã]o)\b/u', $content)) return [[$root, $security, 'Gestão de Identidade e Acesso'], $security];
    if (preg_match('/\b(?:ldap|x\.500)\b/u', $content)) return [[$root, $networks, $protocols, 'LDAP'], $protocols];
    if (preg_match('/\b(?:nfs|samba|cifs)\b/u', $content)) return [[$root, $networks, $protocols, 'Compartilhamento de Arquivos em Rede'], $protocols];
    if (preg_match('/\b(?:active directory|\bad\b|dom[ií]nio windows)\b/u', $content)) return [[$root, $operatingSystems, 'Active Directory'], $operatingSystems];
    if (str_contains(mb_strtolower($name), 'contêiner') || preg_match('/\b(?:docker|kubernetes|podman|container|conteiner)\b/u', $content)) return [[$root, $infrastructure, 'Contêineres'], $infrastructure];
    if (preg_match('/\b(?:hypervisor|virtualiza[cç][aã]o|vmware|máquina virtual|virtual machine)\b/u', $content)) return [[$root, $infrastructure, 'Virtualização'], $infrastructure];
    if (preg_match('/\b(?:cloud|nuvem|iaas|paas|saas|azure|aws|openstack)\b/u', $content)) return [[$root, $infrastructure, 'Computação em Nuvem'], $infrastructure];
    if (preg_match('/\b(?:linux|unix|bash|shell script|chmod|grep|systemd)\b/u', $content)) return [[$root, $operatingSystems, 'Linux e Unix'], $operatingSystems];
    if (preg_match('/\b(?:windows|powershell|ntfs|registry)\b/u', $content)) return [[$root, $operatingSystems, 'Microsoft Windows'], $operatingSystems];
    if (preg_match('/\b(?:arquivo|file system|fat32|ext4|inode|diretório)\b/u', $content)) return [[$root, $operatingSystems, 'Sistemas de Arquivos'], $operatingSystems];
    if (preg_match('/\b(?:processo|thread|escalonamento|deadlock|memória virtual|paginação|kernel)\b/u', $content)) return [[$root, $operatingSystems, 'Gerenciamento de Processos e Memória'], $operatingSystems];
    if (preg_match('/\b(?:storage|armazenamento|raid|nas|san|iscsi|fc )\b/u', $content)) return [[$root, $infrastructure, 'Storage NAS e SAN'], $infrastructure];
    if (preg_match('/\b(?:servidor web|apache|nginx|iis|httpd)\b/u', $content)) return [[$root, $infrastructure, 'Servidores Web'], $infrastructure];
    if (preg_match('/\b(?:distribu[ií]do|cluster|replica[cç][aã]o|consenso|resili)\b/u', $content)) return [[$root, $infrastructure, 'Sistemas Distribuídos'], $infrastructure];
    return [[$root, $infrastructure, 'Arquitetura de Computadores'], $infrastructure];
}
