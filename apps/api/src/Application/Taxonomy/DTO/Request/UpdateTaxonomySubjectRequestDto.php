<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\DTO\Request;
final readonly class UpdateTaxonomySubjectRequestDto { public function __construct(public string $id,public string $name,public ?string $parentId,public ?string $description){} }
