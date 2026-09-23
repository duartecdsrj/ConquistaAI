<?php
declare(strict_types=1);
namespace App\Domain\Study\Entity;
use App\Domain\Study\Enum\NotebookMode;
use App\Domain\Study\ValueObject\FrozenQuestionSelection;
final readonly class Notebook {
    public function __construct(public string $id, public string $userId, public string $name, public NotebookMode $mode, public FrozenQuestionSelection $selection, public \DateTimeImmutable $createdAt) {}
    public static function create(string $userId, string $name, NotebookMode $mode, FrozenQuestionSelection $selection, \DateTimeImmutable $now): self {
        if (trim($name) === '') { throw new \DomainException('Notebook name is required.'); }
        return new self(self::uuid(), $userId, trim($name), $mode, $selection, $now);
    }
    private static function uuid(): string { $b=random_bytes(16);$b[6]=chr((ord($b[6])&15)|64);$b[8]=chr((ord($b[8])&63)|128);return vsprintf('%s%s-%s-%s-%s-%s%s%s',str_split(bin2hex($b),4)); }
}
