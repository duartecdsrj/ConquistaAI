<?php
declare(strict_types=1);
namespace App\Domain\Performance\ValueObject;
final readonly class StudyPlanAnswer { public function __construct(public string $subjectId, public bool $isCorrect, public \DateTimeImmutable $completedAt) {} }
