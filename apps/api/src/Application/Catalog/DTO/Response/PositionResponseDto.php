<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Response;
final readonly class PositionResponseDto { public function __construct(public string $id, public string $examId, public string $name, public ?string $emphasis) {} }
