<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Entity;
final readonly class Subject {public function __construct(public string $id,public string $syllabusId,public ?string $parentId,public string $name,public int $sortOrder){}}
