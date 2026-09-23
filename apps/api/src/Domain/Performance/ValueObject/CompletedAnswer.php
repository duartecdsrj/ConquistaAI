<?php
declare(strict_types=1);
namespace App\Domain\Performance\ValueObject;
final readonly class CompletedAnswer { public function __construct(public string $subjectId, public bool $isCorrect, public int $elapsedSeconds) { if ($elapsedSeconds < 0) { throw new \InvalidArgumentException('Elapsed seconds cannot be negative.'); } } }
