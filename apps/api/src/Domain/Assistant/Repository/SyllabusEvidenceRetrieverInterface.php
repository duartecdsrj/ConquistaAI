<?php
declare(strict_types=1);
namespace App\Domain\Assistant\Repository;
use App\Domain\Assistant\ValueObject\SyllabusEvidence;
interface SyllabusEvidenceRetrieverInterface { /** @return list<SyllabusEvidence> */ public function retrieve(string $syllabusId,string $query,int $limit=3):array; /** @return list<\App\Domain\Assistant\ValueObject\AvailableSyllabus> */ public function listAvailable():array; }
