<?php
declare(strict_types=1);

use App\Domain\Taxonomy\Entity\TaxonomySubject;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionTaxonomyAssignmentRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionRecord;
use App\Infrastructure\Persistence\Doctrine\Taxonomy\DoctrineTaxonomySubjectRepository;

require __DIR__ . '/../vendor/autoload.php';

$entityManager = DoctrineEntityManagerFactory::create();
$taxonomy = new DoctrineTaxonomySubjectRepository($entityManager);
$assignments = new DoctrineQuestionTaxonomyAssignmentRepository($entityManager);
$slugs = new SubjectTaxonomyService();
$parent = $taxonomy->findBySlug($slugs->slug('Governança e Segurança de Dados'));
if ($parent === null) {
    throw new RuntimeException('Pai canônico de Dados Abertos não encontrado.');
}
$subject = $taxonomy->findByParentAndSlug($parent->id, $slugs->slug('Dados Abertos'));
if ($subject === null) {
    $id = sprintf('%s-%s-%s-%s-%s', bin2hex(random_bytes(4)), bin2hex(random_bytes(2)), bin2hex(random_bytes(2)), bin2hex(random_bytes(2)), bin2hex(random_bytes(6)));
    $subject = new TaxonomySubject($id, $parent->id, 'Dados Abertos', $slugs->slug('Dados Abertos'), null, $parent->level + 1, true);
    $taxonomy->save($subject);
}
$questions = $entityManager->createQueryBuilder()
    ->select('question')
    ->from(QuestionRecord::class, 'question')
    ->where('question.origin = :origin')
    ->andWhere('question.status IN (:statuses)')
    ->andWhere('LOWER(question.statement) LIKE :topic')
    ->setParameter('origin', 'EXAM')
    ->setParameter('statuses', ['REVIEW', 'DRAFT'])
    ->setParameter('topic', '%dados abertos%')
    ->getQuery()
    ->getResult();
foreach ($questions as $question) {
    $assignments->replaceForQuestion($question->id, [$subject->id]);
}
$entityManager->flush();
printf("Dados Abertos=%s; questões reclassificadas=%d\n", $subject->id, count($questions));
