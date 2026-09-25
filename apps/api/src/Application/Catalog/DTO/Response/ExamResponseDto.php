<?php
declare(strict_types=1);

namespace App\Application\Catalog\DTO\Response;

final readonly class ExamResponseDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $organizer,
        public ?int $year,
        public ?string $institutionLogoUrl,
        public ?string $organizerLogoUrl,
    ) {
    }
}
