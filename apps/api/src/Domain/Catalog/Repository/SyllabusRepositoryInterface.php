<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Repository;
use App\Domain\Catalog\Entity\Syllabus;
interface SyllabusRepositoryInterface { public function save(Syllabus $syllabus): void; public function findById(string $id): ?Syllabus; public function existsById(string $id): bool; public function existsForPosition(string $id, string $positionId): bool; /** @return list<Syllabus> */ public function listForPosition(string $positionId): array; }
