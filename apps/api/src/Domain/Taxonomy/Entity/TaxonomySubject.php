<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\Entity;

final readonly class TaxonomySubject
{
    public function __construct(
        public string $id,
        public ?string $parentId,
        public string $name,
        public string $slug,
        public ?string $description,
        public int $level,
        public bool $active,
    ) {
        if (trim($id) === '' || trim($name) === '' || trim($slug) === '' || $level < 0) {
            throw new \InvalidArgumentException('Assunto canonico invalido.');
        }
    }
}
