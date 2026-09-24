<?php
declare(strict_types=1);
namespace App\Domain\Performance\ValueObject;
final readonly class SyllabusOption { public function __construct(public string $id, public string $name, public string $positionName, public string $examName) {} }
