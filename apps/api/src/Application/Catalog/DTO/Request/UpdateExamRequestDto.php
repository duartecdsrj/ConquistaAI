<?php
declare(strict_types=1);

namespace App\Application\Catalog\DTO\Request;

final readonly class UpdateExamRequestDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $organizer,
        public ?int $year,
    ) {
    }
}
