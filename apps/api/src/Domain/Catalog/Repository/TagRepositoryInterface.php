<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Repository;
use App\Domain\Catalog\Entity\Tag;
interface TagRepositoryInterface { public function save(Tag $tag): void; /** @return list<Tag> */ public function list(): array; public function existsByName(string $name): bool; }
