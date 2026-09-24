<?php
declare(strict_types=1);
namespace App\Domain\Taxonomy\Repository;
use App\Domain\Taxonomy\Entity\TaxonomySubjectMerge;
interface TaxonomySubjectMergeRepositoryInterface { public function save(TaxonomySubjectMerge $merge):void; }
