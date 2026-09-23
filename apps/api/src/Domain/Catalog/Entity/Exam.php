<?php
declare(strict_types=1);

namespace App\Domain\Catalog\Entity;

final readonly class Exam
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $organizer,
        public ?int $year,
    ) {
    }
}
