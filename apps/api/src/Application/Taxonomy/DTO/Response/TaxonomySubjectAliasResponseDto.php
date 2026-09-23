<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\DTO\Response;
final readonly class TaxonomySubjectAliasResponseDto { public function __construct(public string $id,public string $subjectId,public string $alias){} }
