<?php
declare(strict_types=1);
namespace App\Application\Catalog\DTO\Request;
final readonly class CreatePositionRequestDto { public function __construct(public string $examId, public string $name, public ?string $emphasis) {} }
