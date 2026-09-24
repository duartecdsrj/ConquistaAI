<?php
declare(strict_types=1);
namespace App\Domain\Taxonomy\Repository;
interface TaxonomySubjectMergeApplierInterface { public function reassignCanonicalReferences(string $sourceSubjectId,string $targetSubjectId):void; }
