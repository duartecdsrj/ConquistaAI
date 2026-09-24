<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Repository;
use App\Domain\Catalog\Entity\SyllabusProcessingJob;
interface SyllabusProcessingJobRepositoryInterface { public function save(SyllabusProcessingJob $job):void; public function findLatestForSyllabus(string $syllabusId):?SyllabusProcessingJob; public function claimNext():?SyllabusProcessingJob; }
