<?php
declare(strict_types=1);
namespace App\Domain\QuestionLearning\Repository;
use App\Domain\QuestionLearning\Entity\QuestionComment;
interface QuestionCommentRepositoryInterface { /** @return list<QuestionComment> */ public function listForQuestion(string $questionId, int $page, int $perPage): array; public function countForQuestion(string $questionId): int; public function findForQuestion(string $commentId, string $questionId): ?QuestionComment; public function save(QuestionComment $comment): void; }
