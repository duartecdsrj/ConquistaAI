<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Entity;
final readonly class Tag { public function __construct(public string $id, public string $name) {} }
