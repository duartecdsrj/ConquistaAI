<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionTaxonomyAssignmentRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\DoctrineTaxonomySubjectRepository;

[$script, $jobId] = array_pad($argv, 2, null);
$dryRun = in_array('--dry-run', $argv, true);
if (!is_string($jobId) || $jobId === '') throw new InvalidArgumentException('Informe o identificador do job de importação.');

$em = DoctrineEntityManagerFactory::create();
$subjects = new DoctrineTaxonomySubjectRepository($em);
$assignments = new DoctrineQuestionTaxonomyAssignmentRepository($em);
$questions = $em->createQueryBuilder()->select('question')->from(QuestionRecord::class, 'question')->where('question.sourcePdfJobId = :job')->setParameter('job', $jobId)->orderBy('question.id', 'ASC')->getQuery()->getResult();
$resolved = [];
$counts = [];
foreach ($questions as $question) {
    if (!$question instanceof QuestionRecord) continue;
    $slug = subjectSlug($question->statement);
    $subject = $resolved[$slug] ??= $subjects->findBySlug($slug);
    if ($subject === null) throw new RuntimeException('Assunto canônico indisponível: ' . $slug);
    $counts[$subject->name] = ($counts[$subject->name] ?? 0) + 1;
    if (!$dryRun) $assignments->replaceForQuestion($question->id, [$subject->id]);
}
if (!$dryRun) $em->flush();
ksort($counts, SORT_NATURAL | SORT_FLAG_CASE);
echo json_encode(['questions' => count($questions), 'updated' => !$dryRun, 'subjects' => $counts], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;

function subjectSlug(string $statement): string
{
    $content = mb_strtolower($statement);
    return match (true) {
        matches($content, '/\bipv6\b/u') => 'ipv6',
        matches($content, '/\b(?:modelo\s+osi|osi\b|camada\s+(?:f[ií]sica|enlace|rede|transporte|sess[aã]o|apresenta[cç][aã]o|aplica[cç][aã]o))\b/u') => 'modelo-osi',
        matches($content, '/\b(?:dns|domain\s+name\s+system)\b/u') => 'dns',
        matches($content, '/\b(?:dhcp|dynamic\s+host\s+configuration)\b/u') => 'dhcp',
        matches($content, '/\b(?:nat|network\s+address\s+translation)\b/u') => 'nat',
        matches($content, '/\b(?:vlan|switch(?:ing)?|spanning\s+tree|802\.1q|trunk)\b/u') => 'switching-e-vlan',
        matches($content, '/\b(?:hub|repetidor|repeater|bridge|ponte\s+de\s+rede|modem|placa\s+de\s+rede)\b/u') => 'equipamentos-de-rede',
        matches($content, '/\b(?:ethernet|802\.3|csma)\b/u') => 'ethernet',
        matches($content, '/\b(?:wi-?fi|wireless|802\.11|wlan|rede\s+sem\s+fio|access\s+point|\bwpa2?\b|\bwep\b|bluetooth)\b/u') => 'redes-sem-fio',
        matches($content, '/\b(?:roteamento|roteador|router|ospf|bgp|rip\b|eigrp|gateway\s+padr[aã]o)\b/u') => 'roteamento',
        matches($content, '/\b(?:qos|qualidade\s+de\s+servi[cç]o|diffserv|intserv)\b/u') => 'qualidade-de-servico-qos',
        matches($content, '/\b(?:snmp|wireshark|tcpdump|monitoramento\s+de\s+rede|an[aá]lise\s+de\s+tr[aá]fego)\b/u') => 'monitoramento-de-redes',
        matches($content, '/\b(?:topologia|barramento|estrela|anel|malha|mesh)\b/u') => 'topologias-de-rede',
        matches($content, '/\b(?:cliente\s*[-\\/]?\s*servidor|peer\s*[-\\/]?\s*to\s*[-\\/]?\s*peer|\bp2p\b|arquitetura\s+de\s+rede)\b/u') => 'arquiteturas-de-rede',
        matches($content, '/\b(?:cabeamento|cabo\s+(?:coaxial|utp|stp|par\s+tran[cç]ado)|fibra\s+[oó]ptica|rj-?45|categoria\s*[0-9])\b/u') => 'meios-de-transmissao',
        matches($content, '/\b(?:wan|man\b|atm\b|frame\s+relay|mpls|xdsl|adsl|modula[cç][aã]o|multiplexa[cç][aã]o|\bwdm\b)\b/u') => 'telecomunicacoes',
        matches($content, '/\b(?:firewall|iptables|proxy|waf|dmz)\b/u') => 'firewall-e-proxy',
        matches($content, '/\b(?:ipsec|vpn|openvpn|wireguard|t[uú]nel)\b/u') => 'seguranca-de-redes',
        matches($content, '/\b(?:ssl|tls|https)\b/u') => 'ssl-e-tls',
        matches($content, '/\b(?:criptografia|criptogr[aá]f|aes|rsa|hash|md5|sha-?[0-9]*|chave\s+p[uú]blica|assinatura\s+digital|certifica[cç][aã]o\s+digital)\b/u') => 'criptografia-e-certificacao-digital',
        matches($content, '/\b(?:ids|ips|intrus[aã]o|snort|suricata)\b/u') => 'ids-e-ips',
        matches($content, '/\b(?:biometria|mfa|sso|autentica[cç][aã]o|autoriza[cç][aã]o|controle\s+de\s+acesso)\b/u') => 'gestao-de-identidade-e-acesso',
        matches($content, '/\b(?:vulnerabilidade|gest[aã]o\s+de\s+riscos|iso\s*2700[0-9]|seguran[cç]a\s+da\s+informa[cç][aã]o|continuidade\s+de\s+neg[oó]cios|backup)\b/u') => 'fundamentos-de-seguranca-da-informacao',
        matches($content, '/\b(?:governan[cç]a\s+de\s+ti|cobit|itil)\b/u') => 'governanca-de-ti',
        matches($content, '/\b(?:ldap|x\.500)\b/u') => 'ldap',
        matches($content, '/\b(?:nfs|samba|cifs)\b/u') => 'nfs',
        matches($content, '/\b(?:ftp|sftp)\b/u') => 'ftp',
        matches($content, '/\b(?:smtp|pop3|imap|correio\s+eletr[oô]nico|servidor\s+de\s+e-?mail)\b/u') => 'smtp',
        matches($content, '/\b(?:ssh|secure\s+shell)\b/u') => 'ssh',
        matches($content, '/\btelnet\b/u') => 'telnet',
        matches($content, '/\b(?:http|url|uri|web\s+service)\b/u') => 'http',
        matches($content, '/\b(?:storage|armazenamento|raid|nas|san|iscsi)\b/u') => 'storage-nas-e-san',
        matches($content, '/\b(?:active\s+directory|dom[ií]nio\s+windows)\b/u') => 'active-directory',
        matches($content, '/\b(?:linux|unix|bash|shell\s+script|chmod)\b/u') => 'linux-e-unix',
        matches($content, '/\b(?:windows|powershell|ntfs|registry)\b/u') => 'microsoft-windows',
        matches($content, '/\b(?:virtualiza[cç][aã]o|hypervisor|vmware|m[aá]quina\s+virtual)\b/u') => 'virtualizacao',
        matches($content, '/\b(?:nuvem|cloud|iaas|paas|saas|aws|azure)\b/u') => 'computacao-em-nuvem',
        matches($content, '/\b(?:ipv4|tcp\/ip|\btcp\b|\budp\b|sub-?rede|subnet|m[aá]scara|cidr|endere[cç]o\s+ip)\b/u') => 'tcp-ip',
        default => 'fundamentos-de-redes',
    };
}

function matches(string $content, string $pattern): bool { return preg_match($pattern, $content) === 1; }
