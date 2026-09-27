<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

$apply = in_array('--apply', $argv, true);
$em = DoctrineEntityManagerFactory::create();

/** @var list<QuestionRecord> $questions */
$questions = $em->createQueryBuilder()->select('question')
    ->from(QuestionRecord::class, 'question')
    ->where('question.status = :status')
    ->andWhere('(question.statement LIKE :footer OR question.statement LIKE :courseFooter)')
    ->setParameter('status', 'PUBLISHED')
    ->setParameter('footer', '%Equipe Inform%')
    ->setParameter('courseFooter', '%Concursos da %Fiscal Especialidade TI%')
    ->orderBy('question.id', 'ASC')
    ->getQuery()->getResult();

$changes = [];
foreach ($questions as $question) {
    $clean = removeKnownFooterLines($question->statement);
    if ($clean !== $question->statement) {
        $changes[] = ['id' => $question->id, 'pages' => $question->sourcePdfPages ?? []];
        if ($apply) {
            $question->statement = $clean;
            $question->updatedAt = new DateTimeImmutable('now');
        }
    }
}

if ($apply && $changes !== []) $em->flush();

echo json_encode([
    'mode' => $apply ? 'applied' : 'dry-run',
    'rule' => 'known_publisher_footer_line_only',
    'changed_questions' => count($changes),
    'questions' => $changes,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;

/** Removes only a standalone publisher footer; it never repairs, translates or rewrites text. */
function removeKnownFooterLines(string $value): string
{
    $clean = preg_replace(
        "/^\h*(?:[\p{L}. ]+,\h*)?Equipe\h+Inform.{0,8}tica\h+e\h+TI(?:,\h*[\p{L}. ]+)?\h*\R?|^\h*Concursos da .{0,8}rea Fiscal Especialidade TI[^\n]*\R?/mu",
        "",
        $value,
    );
    if ($clean === null || $clean === $value) return $value;
    $clean = (string) preg_replace("/\n{3,}/u", "\n\n", $clean);
    return trim($clean);
}
