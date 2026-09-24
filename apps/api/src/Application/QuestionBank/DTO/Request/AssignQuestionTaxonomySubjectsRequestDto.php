<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Request;
final readonly class AssignQuestionTaxonomySubjectsRequestDto { /** @param list<string> $taxonomySubjectIds */ public function __construct(public string $questionId,public array $taxonomySubjectIds){} }
