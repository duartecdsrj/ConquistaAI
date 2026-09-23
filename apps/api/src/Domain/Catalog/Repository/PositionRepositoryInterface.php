<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Repository;
use App\Domain\Catalog\Entity\Position;
interface PositionRepositoryInterface {
 public function save(Position $position): void;
 public function existsForExam(string $id, string $examId): bool;
 public function existsById(string $id): bool;
 /** @return list<Position> */ public function listForExam(string $examId): array;
}
