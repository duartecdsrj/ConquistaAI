<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Request;
final readonly class AssignSubjectTaxonomySubjectsRequestDto { /** @param list<string> $taxonomySubjectIds */ public function __construct(public string $subjectId, public array $taxonomySubjectIds) {} }
