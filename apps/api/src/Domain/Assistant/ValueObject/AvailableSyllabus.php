<?php
declare(strict_types=1);
namespace App\Domain\Assistant\ValueObject;
final readonly class AvailableSyllabus { public function __construct(public string $id,public string $name) {} }
