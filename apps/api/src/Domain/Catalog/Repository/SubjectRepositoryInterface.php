<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Repository;
use App\Domain\Catalog\Entity\Subject;
interface SubjectRepositoryInterface { public function save(Subject $subject): void; public function exists(string $id): bool; public function existsForSyllabus(string $id, string $syllabusId): bool; /** @return list<Subject> */ public function listForSyllabus(string $syllabusId): array; }
