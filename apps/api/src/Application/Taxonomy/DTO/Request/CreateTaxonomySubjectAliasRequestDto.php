<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\DTO\Request;
final readonly class CreateTaxonomySubjectAliasRequestDto { public function __construct(public string $subjectId,public string $alias){} }
