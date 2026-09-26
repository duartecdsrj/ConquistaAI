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
$entityManager = DoctrineEntityManagerFactory::create();
$job = $entityManager->find(QuestionPdfImportJobRecord::class, $jobId);
if (!$job instanceof QuestionPdfImportJobRecord) {
    throw new RuntimeException('Job não encontrado.');
}

$taxonomy = new DoctrineTaxonomySubjectRepository($entityManager);
$writer = new DoctrineQuestionPdfQuestionWriter(
    $entityManager,
    new DoctrineQuestionDuplicateDetector($entityManager),
    $taxonomy,
    new DoctrineQuestionTaxonomyAssignmentRepository($entityManager),
    new SubjectTaxonomyService(),
    new ImportedQuestionContentSanitizer(),
);
$pages = (new PdftotextPdfTextExtractor())->extractPages($job->documentPath);
$starts = [];
$document = '';
foreach ($pages as $index => $page) {
    $starts[] = ['offset' => strlen($document), 'page' => $index + 1];
    $document .= "\n" . $page;
}

$boardPattern = '(?:FGV|FCC|CESGRANRIO|VUNESP|QUADRIX|IBFC|AOCP|CONSULPLAN|CEBRASPE|CESPE|FUNDATEC|IADES|COPEVE|COMPERVE|ESAF)';
$headerPattern = '/^\h*\d{1,3}\.\h+\(?'.$boardPattern.'\b/miu';
preg_match_all($headerPattern, $document, $headerMatches, PREG_OFFSET_CAPTURE);
$questions = [];
for ($index = 0, $total = count($headerMatches[0]); $index < $total; $index++) {
    $offset = $headerMatches[0][$index][1];
    $end = $index + 1 < $total ? $headerMatches[0][$index + 1][1] : strlen($document);
    $block = substr($document, $offset, $end - $offset);
    if (!preg_match('/^\h*\d{1,3}\.\h+\(?'.'(?<meta>'.$boardPattern.'[^\n]{0,700})\)?\h*\n(?<body>[\s\S]*)$/iu', $block, $head)) {
        continue;
    }
    if (!preg_match('/^(?<statement>[\s\S]{30,}?)\n\h*[Aa][)\.\h]+(?<a>[\s\S]*?)\n\h*[Bb][)\.\h]+(?<b>[\s\S]*?)\n\h*[Cc][)\.\h]+(?<c>[\s\S]*?)\n\h*[Dd][)\.\h]+(?<d>[\s\S]*?)\n\h*[Ee][)\.\h]+(?<e>[\s\S]*?)(?=\n\h*(?:Comentários:|Gabarito:|$))/msu', $head['body'], $parts)) {
        continue;
    }

    $metaLine = $head['meta'];
    $opening = '';
    if (preg_match('/^(?<identity>.*?\b20\d{2}\)?)(?:\h+)(?<opening>.+)$/u', $metaLine, $inlineStatement)) {
        $metaLine = $inlineStatement['identity'];
        $opening = $inlineStatement['opening'];
    }
    $statement = cleanPdf($opening . "\n" . $parts['statement']);
    $options = [];
    foreach (['a', 'b', 'c', 'd', 'e'] as $label) {
        $options[] = ['content' => cleanPdf($parts[$label])];
    }
    if (mb_strlen($statement) < 35 || array_filter($options, static fn (array $option): bool => $option['content'] === '')) {
        continue;
    }

    $meta = preg_replace('/\s+/u', ' ', trim($metaLine)) ?? '';
    $board = null;
    if (preg_match('/\b(?<board>'.$boardPattern.')\b/iu', $meta, $boardMatch)) {
        $board = strtoupper($boardMatch['board']);
    }
    $year = preg_match('/\b(20\d{2})\b/u', $meta, $yearMatch) ? (int) $yearMatch[1] : null;
    $exam = trim((string) preg_replace('/^\(?'.$boardPattern.'\)?\h*(?:[-–—]\h*)?/iu', '', $meta));
    $content = mb_strtolower($statement . ' ' . implode(' ', array_column($options, 'content')));
    [$path, $parent] = placement($content);
    $page = pageForOffset($starts, $offset);
    $answer = answerFromBlock($block);
    $questions[] = [
        'type' => 'MULTIPLE_CHOICE',
        'statement' => $statement,
        'options' => $options,
        'correct_option' => $answer,
        'answer_key_source' => $answer === null ? null : 'OFFICIAL',
        'board' => $board,
        'exam' => $exam === '' ? null : $exam,
        'year' => $year,
        'taxonomy_path' => $path,
        'parent_subject' => $parent,
        'pages' => [$page],
        'difficulty' => mb_strlen($statement) > 900 ? 'HARD' : (mb_strlen($statement) > 420 ? 'MEDIUM' : 'EASY'),
    ];
}

if ($dryRun) {
    echo json_encode(['candidates' => count($questions), 'sample' => array_slice($questions, 0, 3)], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
    exit;
}

$result = ['created' => 0, 'duplicates' => 0, 'classified' => 0, 'failed' => 0, 'createdSubjects' => 0];
foreach ($questions as $question) {
    $partial = $writer->write($job->createdBy, [$question], [], $job->id);
    foreach ($result as $key => $value) {
        $result[$key] += $partial[$key];
    }
}
$job->status = 'COMPLETED';
$job->progress = 100;
$job->pageCount = count($pages);
$job->candidatePages = count($pages);
$job->extractedQuestions = count($questions);
$job->classifiedQuestions = $result['classified'];
$job->createdQuestions = $result['created'];
$job->duplicateQuestions = $result['duplicates'];
$job->failedQuestions = $result['failed'];
$job->createdTaxonomySubjects = $result['createdSubjects'];
$job->errorMessage = 'Extração direta determinística de questões objetivas de Redes; itens de certo/errado e discursivos foram descartados.';
$job->finishedAt = new DateTimeImmutable('now');
$entityManager->flush();
echo json_encode(['extracted' => count($questions), 'result' => $result], JSON_UNESCAPED_UNICODE) . PHP_EOL;

function cleanPdf(string $value): string
{
    $value = preg_replace('/^.*(?:www\.estrategiaconcursos\.com\.br|André Castro|Equipe Informática e TI|Aula \d{2}|==10045d==).*$/mu', '', $value) ?? $value;
    return trim((string) preg_replace('/\n{3,}/u', "\n\n", $value));
}

/** @param list<array{offset:int,page:int}> $starts */
function pageForOffset(array $starts, int $offset): int
{
    $page = 1;
    foreach ($starts as $start) {
        if ($start['offset'] > $offset) break;
        $page = $start['page'];
    }
    return $page;
}

/** @return array{list<string>,string} */
function placement(string $content): array
{
    $root = 'Tecnologia da Informação';
    $network = 'Redes de Computadores';
    if (preg_match('/\b(?:dns|domain name system)\b/u', $content)) return [[$root, $network, 'Protocolos', 'DNS'], 'Protocolos'];
    if (preg_match('/\b(?:dhcp|dynamic host configuration)\b/u', $content)) return [[$root, $network, 'Protocolos', 'DHCP'], 'Protocolos'];
    if (preg_match('/\b(?:ftp|sftp|tftp)\b/u', $content)) return [[$root, $network, 'Protocolos', 'FTP'], 'Protocolos'];
    if (preg_match('/\b(?:http|https|web)\b/u', $content)) return [[$root, $network, 'Protocolos', 'HTTP'], 'Protocolos'];
    if (preg_match('/\b(?:smtp|pop3|imap|correio eletr)\b/u', $content)) return [[$root, $network, 'Protocolos', 'SMTP'], 'Protocolos'];
    if (preg_match('/\b(?:snmp|gerência de rede)\b/u', $content)) return [[$root, $network, 'Protocolos', 'SNMP'], 'Protocolos'];
    if (preg_match('/\b(?:ssh|telnet|acesso remoto)\b/u', $content)) return [[$root, $network, 'Protocolos', 'SSH'], 'Protocolos'];
    if (preg_match('/\b(?:tcp|udp|ip\b|icmp|ipv4|ipv6)\b/u', $content)) return [[$root, $network, 'Protocolos', 'TCP/IP'], 'Protocolos'];
    if (preg_match('/\b(?:osi|camada (?:física|enlace|rede|transporte|sessão|apresentação|aplica))\b/u', $content)) return [[$root, $network, 'Modelo OSI'], $network];
    if (preg_match('/\b(?:ospf|bgp|rip|roteamento|roteador|gateway)\b/u', $content)) return [[$root, $network, 'Roteamento'], $network];
    if (preg_match('/\b(?:vlan|switch|comutação|spanning tree|stp)\b/u', $content)) return [[$root, $network, 'Switching e VLAN'], $network];
    if (preg_match('/\b(?:ethernet|ieee 802\.3|csma)\b/u', $content)) return [[$root, $network, 'Ethernet'], $network];
    if (preg_match('/\b(?:wi-?fi|wireless|802\.11|wlan|bluetooth)\b/u', $content)) return [[$root, $network, 'Redes sem Fio'], $network];
    if (preg_match('/\b(?:anel|estrela|malha|barramento|topologia)\b/u', $content)) return [[$root, $network, 'Topologias de Rede'], $network];
    if (preg_match('/\b(?:qos|qualidade de servi)\b/u', $content)) return [[$root, $network, 'Qualidade de Serviço (QoS)'], $network];
    if (preg_match('/\b(?:fibra|cabo|utp|coaxial|par trançado|transmissão|modulação|sinal)\b/u', $content)) return [[$root, $network, 'Meios de Transmissão'], $network];
    return [[$root, $network, 'Fundamentos de Redes'], $network];
}

function answerFromBlock(string $block): ?string
{
    return preg_match('/\bGabarito:\h*(?:Letra\h*)?([A-E])\b/iu', $block, $match) ? strtoupper($match[1]) : null;
}
