<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

const QUESTION_ID = '0025b8cc-c6f9-407f-99ba-e5b8de21be61';
const OPTION_A_ID = '437edbd7-24d1-4617-873c-df259c77aebb';
const OPTION_A_ANSWER = 'F – V – F – V.';

$apply = in_array('--apply', $argv, true);
$em = DoctrineEntityManagerFactory::create();
$question = $em->find(QuestionRecord::class, QUESTION_ID);
$option = $em->find(QuestionOptionRecord::class, OPTION_A_ID);

if (!$question instanceof QuestionRecord || !$option instanceof QuestionOptionRecord) {
    throw new RuntimeException('Questão ou alternativa de origem não encontrada.');
}

if ($question->status !== 'PUBLISHED' || $option->questionId !== QUESTION_ID || $option->label !== 'A') {
    throw new RuntimeException('A proveniência ou a estrutura da questão não corresponde à correção delimitada.');
}

$changed = false;
if (trim($option->content) !== OPTION_A_ANSWER) {
    [$prefix, $answer] = splitLeakedOption($option->content);
    $statement = rtrim($question->statement) . "\n\nA " . $prefix;
    $changed = $question->statement !== $statement || $option->content !== $answer;
} elseif (!str_contains($question->statement, "\n\nA esse respeito,")) {
    throw new RuntimeException('A alternativa A já está reduzida, mas o enunciado não contém o trecho verificado.');
}

if ($apply && $changed) {
    $question->statement = $statement;
    $question->updatedAt = new DateTimeImmutable('now');
    $option->content = $answer;
    $em->flush();
}

echo json_encode([
    'mode' => $apply ? 'applied' : 'dry-run',
    'rule' => 'verified_option_a_statement_leak_from_source_reference_883',
    'question_id' => QUESTION_ID,
    'changed' => $changed,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;

/** @return array{string, string} */
function splitLeakedOption(string $content): array
{
    $parts = explode("\n\nA) ", $content, 2);
    if (count($parts) !== 2 || trim($parts[1]) !== OPTION_A_ANSWER || !str_contains($parts[0], '( )')) {
        throw new RuntimeException('O conteúdo da alternativa A não corresponde ao trecho verificado no PDF de origem.');
    }

    return [trim($parts[0]), OPTION_A_ANSWER];
}
