<?php
declare(strict_types=1);
namespace App\Domain\Study\ValueObject;
final readonly class FrozenQuestionSelection {
    /** @param list<string> $questionIds */
    private function __construct(public array $questionIds) {}
    /** @param list<string> $questionIds */
    public static function fromQuestionIds(array $questionIds, int $expectedQuantity): self {
        $ids = array_values(array_unique(array_filter($questionIds, static fn (mixed $id): bool => is_string($id) && $id !== '')));
        if ($expectedQuantity < 1 || count($ids) !== $expectedQuantity) { throw new \DomainException('Insufficient questions for the requested notebook.'); }
        return new self($ids);
    }
}
