<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Repository;
use App\Domain\QuestionLearning\Entity\QuestionNote;
interface QuestionNoteRepositoryInterface { public function findForUserQuestion(string $userId, string $questionId): ?QuestionNote; public function save(QuestionNote $note): void; public function deleteForUserQuestion(string $userId, string $questionId): void; }
