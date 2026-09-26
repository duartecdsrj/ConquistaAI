<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\Assistant\ValueObject\SyllabusEvidence;
use App\Infrastructure\Assistant\ConfiguredAssistantProviderFactory;
use App\Infrastructure\Extraction\PdftotextPdfTextExtractor;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionPdfImportJobRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

$jobId = $argv[1] ?? '';
$limit = max(1, (int) (preg_grep('/^--limit=/', $argv) ? substr((string) current(preg_grep('/^--limit=/', $argv)), 8) : 1000));
$dryRun = in_array('--dry-run', $argv, true);
$entityManager = DoctrineEntityManagerFactory::create();
$job = $entityManager->find(QuestionPdfImportJobRecord::class, $jobId);
if (!$job instanceof QuestionPdfImportJobRecord) throw new RuntimeException('Job não encontrado.');

$pages = (new PdftotextPdfTextExtractor())->extractPages($job->documentPath);
$provider = ConfiguredAssistantProviderFactory::create();
$questions = $entityManager->createQueryBuilder()->select('question')->from(QuestionRecord::class, 'question')
    ->where('question.sourcePdfJobId=:job')->andWhere('question.correctOptionId IS NULL')->setParameter('job', $jobId)
    ->orderBy('question.createdAt', 'ASC')->setMaxResults($limit)->getQuery()->getResult();
$updated = $undetermined = $failed = $processed = 0;
foreach ($questions as $question) {
    if (!$question instanceof QuestionRecord) continue;
    $processed++;
    $options = $entityManager->createQueryBuilder()->select('option')->from(QuestionOptionRecord::class, 'option')
        ->where('option.questionId=:question')->setParameter('question', $question->id)->orderBy('option.sortOrder', 'ASC')->getQuery()->getResult();
    $renderedOptions = implode("\n", array_map(static fn (QuestionOptionRecord $option): string => $option->label . ') ' . $option->content, $options));
    $page = (int) (($question->sourcePdfPages ?? [1])[0] ?? 1);
    $evidence = [];
    foreach (array_slice($pages, max(0, $page - 1), 6, true) as $index => $content) $evidence[] = new SyllabusEvidence($index + 1, mb_substr($content, 0, 12000));
    $prompt = "Resolva a questão de múltipla escolha abaixo com rigor técnico. Use o enunciado, as alternativas e o material fornecido. Não suponha dados ausentes. Retorne exatamente `RESPOSTA: X` (X entre A e E) se houver uma única alternativa correta; caso contrário, retorne `INDETERMINADO`.\n\nENUNCIADO:\n{$question->statement}\n\nALTERNATIVAS:\n{$renderedOptions}";
    try {
        $answer = $provider->answer($prompt, $evidence)->content;
    } catch (Throwable $error) {
        fwrite(STDERR, "Falha para {$question->id}: {$error->getMessage()}\n");
        $failed++;
        break;
    }
    if (preg_match('/\bRESPOSTA\s*:\s*([A-E])\b/iu', $answer, $match) !== 1) {
        $undetermined++;
        continue;
    }
    $label = strtoupper($match[1]);
    $option = array_values(array_filter($options, static fn (QuestionOptionRecord $option): bool => $option->label === $label))[0] ?? null;
    if (!$option instanceof QuestionOptionRecord) {
        $undetermined++;
        continue;
    }
    if (!$dryRun) {
        $question->correctOptionId = $option->id;
        $question->answerKeySource = 'AI_ESTIMATED';
        $question->updatedAt = new DateTimeImmutable('now');
        $entityManager->flush();
    }
    $updated++;
    fwrite(STDOUT, json_encode(['question_id' => $question->id, 'answer' => $label], JSON_UNESCAPED_UNICODE) . PHP_EOL);
    usleep(500000);
}
echo json_encode(['processed' => $processed, 'updated_ai_estimated' => $updated, 'undetermined' => $undetermined, 'failed' => $failed, 'dry_run' => $dryRun], JSON_UNESCAPED_UNICODE) . PHP_EOL;
