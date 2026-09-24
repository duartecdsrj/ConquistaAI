<?php
declare(strict_types=1);
namespace App\Domain\Assistant\ValueObject;
final readonly class SyllabusEvidence { public function __construct(public int $pageNumber, public string $excerpt) {} }
