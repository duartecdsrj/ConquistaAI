<?php
declare(strict_types=1);

namespace Tests\Integration\QuestionLearning;

use App\Domain\Identity\Entity\User;
use App\Domain\QuestionLearning\Entity\QuestionExplanationExecution;
use App\Domain\QuestionLearning\Entity\QuestionNote;
use App\Domain\QuestionLearning\Enum\ExplanationSafetyMode;
use App\Domain\QuestionLearning\Enum\ExplanationStatus;
use App\Infrastructure\Persistence\Doctrine\DoctrineEntityManagerFactory;
use App\Infrastructure\Persistence\Doctrine\Identity\DoctrineUserRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionLearning\DoctrineQuestionExplanationExecutionRepository;
use App\Infrastructure\Persistence\Doctrine\QuestionLearning\DoctrineQuestionNoteRepository;
use PHPUnit\Framework\TestCase;

final class DoctrineQuestionLearningRepositoryTest extends TestCase
{
    public function testNotesAndExplanationsRemainIsolatedByOwner(): void
    {
        $entityManager = DoctrineEntityManagerFactory::create();
        $connection = $entityManager->getConnection();
        $suffix = bin2hex(random_bytes(6));
        $userA = '60000000-0000-4000-8000-'.substr($suffix, 0, 12);
        $userB = '60000000-0000-4000-8001-'.substr($suffix, 0, 12);
        $question = '70000000-0000-4000-8000-'.substr($suffix, 0, 12);
        $now = new \DateTimeImmutable('2026-01-01T00:00:00Z');
        $users = new DoctrineUserRepository($entityManager);
        $notes = new DoctrineQuestionNoteRepository($entityManager);
        $explanations = new DoctrineQuestionExplanationExecutionRepository($entityManager);
        $connection->beginTransaction();
        try {
            $users->save(new User($userA, 'learning-a-'.$suffix.'@example.test', 'Learning A', null, 'ACTIVE', ['USER']));
            $users->save(new User($userB, 'learning-b-'.$suffix.'@example.test', 'Learning B', null, 'ACTIVE', ['USER']));
            $entityManager->flush();
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');
            $connection->executeStatement("INSERT INTO questions (id,syllabus_id,statement,difficulty,origin,status,created_by,created_at,updated_at) VALUES (:id,:syllabus,'Fixture','EASY','EXAM','PUBLISHED',:user,UTC_TIMESTAMP(),UTC_TIMESTAMP())", ['id' => $question, 'syllabus' => '80000000-0000-4000-8000-'.substr($suffix, 0, 12), 'user' => $userA]);
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
            $notes->save(new QuestionNote('90000000-0000-4000-8000-'.substr($suffix, 0, 12), $userA, $question, 'nota A', $now, $now));
            $notes->save(new QuestionNote('90000000-0000-4000-8001-'.substr($suffix, 0, 12), $userB, $question, 'nota B', $now, $now));
            $explanations->save(new QuestionExplanationExecution('a0000000-0000-4000-8000-'.substr($suffix, 0, 12), $userA, $question, null, 'v1', ExplanationSafetyMode::CONCEPTUAL_ONLY, ExplanationStatus::PENDING, 0, null, null, null, null, null, null, null, $now));
            $explanations->save(new QuestionExplanationExecution('a0000000-0000-4000-8001-'.substr($suffix, 0, 12), $userB, $question, null, 'v1', ExplanationSafetyMode::CONCEPTUAL_ONLY, ExplanationStatus::PENDING, 0, null, null, null, null, null, null, null, $now));
            $entityManager->clear();
            self::assertSame('nota A', $notes->findForUserQuestion($userA, $question)?->content());
            self::assertSame('nota B', $notes->findForUserQuestion($userB, $question)?->content());
            self::assertSame($userA, $explanations->findLatestForUserQuestion($userA, $question)?->userId);
            self::assertSame($userB, $explanations->findLatestForUserQuestion($userB, $question)?->userId);
        } finally {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
            $connection->rollBack();
            $entityManager->clear();
        }
    }
}
