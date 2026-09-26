<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionOptionRecord;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;

/**
 * Operação editorial explícita: atribui estimativas geradas por IA onde o PDF
 * não forneceu gabarito. Nunca substitui um gabarito existente ou oficial.
 */
$answers = [
  'f1c6721b-45b8-49b7-bd37-66c5bb09c9f7' => 'E',
  '00f6f7a8-afec-4992-bb7a-d4d1be92da56' => 'E',
  '8234a46e-20ec-4ba5-9d1c-16d89e5421ce' => 'D',
  '610723c1-57d3-4731-9f5e-0e6e63295764' => 'D',
  '90342dbc-f33c-44a2-ab57-f07cea47e8bb' => 'B',
  '423bbb47-376e-4759-a642-c353b6b2933a' => 'D',
  '32e87978-d8fb-4141-ad00-68604ee5dc4c' => 'A',
  '2db3f73f-7a6a-4390-a2a4-bc9611763fb6' => 'E',
  '120b2161-24b9-4280-a8d1-c0077241f44a' => 'D',
  'f156866f-e149-468d-a508-7b2eb3614331' => 'C',
  'e761fc42-2ed1-4b6e-8df7-b9f25c4cb88e' => 'C',
  'e415501b-2a93-4796-b8f7-16e39dc43816' => 'A',
  'e81e7bf0-ef32-44d2-88c1-5e8a8eae6970' => 'D',
  'dea95664-cf14-4e00-aa02-c973860f4438' => 'A',
  'be6c804e-81c8-4a0e-8ef8-6bb1fc2e7f1a' => 'B',
  'b907c626-034d-4428-97d2-bfc15ea554e3' => 'D',
  'a033986c-a529-4b6f-adb8-f9cede0e9e1b' => 'B',
  'ef20b21c-29aa-4442-98de-c0bc14c78819' => 'C',
  '8b561fb6-1ccb-4082-9dea-66fa766398ca' => 'B',
  '82aeeb5b-3d88-4a5d-beab-97b464a42a3d' => 'D',
  '821f71b2-559e-4d2f-9d55-c7654f99885d' => 'A',
  '770b5691-c8a8-40d1-b11d-a1ba1aa86444' => 'B',
  '044bc247-3a6a-44e1-b4ec-ef5772834d11' => 'A',
  '6cbec9b8-3525-4f07-820e-088cd81a1120' => 'E',
  '653f7afe-0ef6-4683-adb7-c1f3dd7a87bf' => 'C',
  '6133ccfe-e2fc-4b01-a302-9cc284f0c640' => 'A',
  '6002b7e7-ee3e-452f-a628-0eae579048d0' => 'C',
  '0a4dd981-0c41-4999-8e7d-b0719fd46acc' => 'C',
  '5ebfd79b-b279-4556-b19c-44f76b848569' => 'B',
  '15cf2884-b382-4071-a536-e14fb7ff972a' => 'E',
  '79433d33-9cfb-400d-abf1-01e064f253f2' => 'D',
  '4aafd688-eb61-4332-9fb6-ba2360a69393' => 'B',
  '23e16804-9cc5-4210-b7b0-b76a56e57c0e' => 'D',
  'bd93dc65-0691-4068-ac2d-db8cfd958515' => 'D',
  '0cd7d003-fb90-4f6d-b492-46b612bdd5d3' => 'B',
  '35be8045-9334-40c3-87a7-f994c75bdd76' => 'C',
  '4a4c3947-009f-4eab-8b6f-8c25cd92707e' => 'D',
  'd2503e96-b662-4390-834a-036ba0650091' => 'B',
  '553645f0-c0f4-43f2-8c1c-43975f74f02b' => 'B',
  '9d6d1379-462c-49aa-8d35-36ddbc566392' => 'C',
  '96271962-8d4b-4b52-a842-dbc88891fed8' => 'B',
  '6f3f5c29-f984-436b-9ac3-af6054955e80' => 'B'
];
$em = DoctrineEntityManagerFactory::create();
$updated = 0;
$em->beginTransaction();
try {
    foreach ($answers as $questionId => $label) {
        $question = $em->find(QuestionRecord::class, $questionId);
        if (!$question instanceof QuestionRecord || $question->correctOptionId !== null) {
            throw new RuntimeException('Questão inválida para estimativa: ' . $questionId);
        }
        $option = $em->createQueryBuilder()->select('o')->from(QuestionOptionRecord::class, 'o')
            ->where('o.questionId = :questionId')->andWhere('o.label = :label')
            ->setParameter('questionId', $questionId)->setParameter('label', $label)->getQuery()->getOneOrNullResult();
        if (!$option instanceof QuestionOptionRecord) throw new RuntimeException('Alternativa não encontrada: ' . $questionId);
        $question->correctOptionId = $option->id;
        $question->answerKeySource = 'AI_ESTIMATED';
        $question->updatedAt = new DateTimeImmutable('now');
        $updated++;
    }
    if ($updated !== 42) throw new RuntimeException('Quantidade inesperada: ' . $updated);
    $em->flush();
    $em->commit();
    echo json_encode(['updated' => $updated, 'source' => 'AI_ESTIMATED'], JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    $em->rollback();
    throw $e;
}
