<?php
declare(strict_types=1);
namespace App\Application\Taxonomy\DTO\Request;
final readonly class CreateTaxonomySubjectRequestDto { public function __construct(public string $name,public ?string $parentId,public ?string $description) {} }
