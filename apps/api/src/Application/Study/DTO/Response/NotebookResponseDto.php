<?php
declare(strict_types=1);
namespace App\Application\Study\DTO\Response;
final readonly class NotebookResponseDto {
    /** @param list<string> $questionIds */
    public function __construct(public string $id, public string $name, public string $mode, public array $questionIds, public string $createdAt) {}
}
