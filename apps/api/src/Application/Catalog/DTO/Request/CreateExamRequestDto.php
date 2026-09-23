<?php
declare(strict_types=1);

namespace App\Application\Catalog\DTO\Request;

final readonly class CreateExamRequestDto
{
    public function __construct(
        public string $name,
        public ?string $organizer,
        public ?int $year,
    ) {
    }
}
