<?php
declare(strict_types=1);
namespace App\Domain\Taxonomy\Repository;
use App\Domain\Taxonomy\Entity\TaxonomySubjectAlias;
interface TaxonomySubjectAliasRepositoryInterface { public function save(TaxonomySubjectAlias $alias):void; public function findByNormalizedAlias(string $normalizedAlias):?TaxonomySubjectAlias; }
