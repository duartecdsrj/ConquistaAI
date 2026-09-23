<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Response;
final readonly class SyllabusResponseDto { public function __construct(public string $id, public string $positionId, public string $name, public ?string $publishedAt, public ?string $sourceUrl) {} }
