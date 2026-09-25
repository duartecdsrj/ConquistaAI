<?php
declare(strict_types=1);

namespace App\Application\Catalog\Port;

interface ExamLogoResearcherInterface
{
    /** @return array{institution: ?string, organizer: ?string} */
    public function find(string $examName, ?string $organizer): array;
}
