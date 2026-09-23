<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Entity;
final readonly class Position { public function __construct(public string $id, public string $examId, public string $name, public ?string $emphasis) {} }
