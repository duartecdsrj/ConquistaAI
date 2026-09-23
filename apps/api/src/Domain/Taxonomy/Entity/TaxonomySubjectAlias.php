<?php
declare(strict_types=1);
namespace App\Domain\Taxonomy\Entity;
final readonly class TaxonomySubjectAlias { public function __construct(public string $id,public string $subjectId,public string $alias,public string $normalizedAlias){} }
