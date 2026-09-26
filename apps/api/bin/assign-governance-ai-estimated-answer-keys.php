<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

/** Aplica somente estimativas editoriais de IA a questões sem gabarito oficial. */
$answers = [
    '11477f8a-c96a-411b-9f9f-925e65309d01' => 'A',
    '27b54368-281a-4cb9-b3d6-2e4609951edc' => 'B',
    '3bc29763-623e-42c1-8561-56d82e01b9fd' => 'B',
    '5a1bf0f6-6592-4160-8256-9415408cc913' => 'C',
    '84ec6526-8535-47a5-8226-ecde43a0d6bc' => 'B',
    'b00a4172-5fb6-4632-8b16-74794a122970' => 'E',
    'b841dd60-179b-4bc0-87c5-4d0516ab333a' => 'C',
    'd5aa556f-25d0-481c-af5c-2cb31e0620ca' => 'A',
    'e7e39a91-cb77-41c7-8292-c00483e5ab3c' => 'D',
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
