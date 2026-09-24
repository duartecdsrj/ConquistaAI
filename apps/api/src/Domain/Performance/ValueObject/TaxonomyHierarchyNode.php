<?php
declare(strict_types=1);
namespace App\Domain\Performance\ValueObject;
final readonly class TaxonomyHierarchyNode { public function __construct(public string $id, public ?string $parentId, public string $name) {} }
