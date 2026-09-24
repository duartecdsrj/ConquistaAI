<?php
declare(strict_types=1);
namespace App\Domain\Performance\ValueObject;
final readonly class SyllabusCompletedAnswer { /** @param list<string> $taxonomySubjectIds */ public function __construct(public bool $isCorrect, public int $elapsedSeconds, public \DateTimeImmutable $completedAt, public array $taxonomySubjectIds) {} }
