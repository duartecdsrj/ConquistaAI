<?php
declare(strict_types=1);
namespace App\Application\Study\DTO\Request;
/** @phpstan-type QuestionIdList list<string> */
final readonly class CreateNotebookRequestDto {
    /** @param list<string> $questionIds */
    public function __construct(public string $userId, public string $name, public string $mode, public int $quantity, public array $questionIds) {}
}
