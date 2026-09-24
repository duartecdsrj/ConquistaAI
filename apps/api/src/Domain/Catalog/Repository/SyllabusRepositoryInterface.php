<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Repository;
interface SyllabusRepositoryInterface { public function save(Syllabus $syllabus): void; public function findById(string $id): ?Syllabus; public function existsById(string $id): bool; public function existsForExam(string $id, string $examId): bool; /** @return list<Syllabus> */ public function listForExam(string $examId): array; }
interface SyllabusRepositoryInterface { public function save(Syllabus $syllabus): void; public function findById(string $id): ?Syllabus; public function existsById(string $id): bool; public function existsForPosition(string $id, string $positionId): bool; /** @return list<Syllabus> */ public function listForPosition(string $positionId): array; }
