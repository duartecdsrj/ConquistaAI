<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

/** Aplica somente estimativas editoriais de IA a questões sem gabarito oficial. */
$answers = [
    'c71bd06b-7568-4084-bb61-87a63a016caf' => 'D',
    '02bb3a6a-2b0a-4212-8f98-45a86af462f8' => 'E',
    '85e58464-f922-465a-aa1b-04a1d5212161' => 'E',
];
$em = DoctrineEntityManagerFactory::create();
$em->beginTransaction();
try {
    foreach ($answers as $questionId => $label) {
        $question = $em->find(QuestionRecord::class, $questionId);
        if (!$question instanceof QuestionRecord || $question->correctOptionId !== null) throw new RuntimeException('Questão inválida: '.$questionId);
        $option = $em->createQueryBuilder()->select('o')->from(QuestionOptionRecord::class, 'o')->where('o.questionId = :questionId')->andWhere('o.label = :label')->setParameter('questionId', $questionId)->setParameter('label', $label)->getQuery()->getOneOrNullResult();
        if (!$option instanceof QuestionOptionRecord) throw new RuntimeException('Alternativa não encontrada: '.$questionId);
        $question->correctOptionId = $option->id;
        $question->answerKeySource = 'AI_ESTIMATED';
        $question->updatedAt = new DateTimeImmutable('now');
    }
    $em->flush();
    $em->commit();
    echo json_encode(['updated' => count($answers), 'source' => 'AI_ESTIMATED'], JSON_UNESCAPED_UNICODE).PHP_EOL;
} catch (Throwable $error) { $em->rollback(); throw $error; }
