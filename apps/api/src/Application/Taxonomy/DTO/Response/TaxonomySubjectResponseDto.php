<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\DTO\Response;
final readonly class TaxonomySubjectResponseDto { public function __construct(public string $id,public ?string $parentId,public string $name,public string $slug,public ?string $description,public int $level,public bool $active) {} }
