<?php
declare(strict_types=1);
namespace App\Domain\QuestionBank\Repository;
interface QuestionTaxonomyAssignmentRepositoryInterface { /** @param list<string> $taxonomySubjectIds */ public function replaceForQuestion(string $questionId, array $taxonomySubjectIds): void; /** @return list<string> */ public function listSubjectIdsForQuestion(string $questionId): array; }
