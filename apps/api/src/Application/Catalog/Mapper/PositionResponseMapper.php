<?php
declare(strict_types=1);
namespace App\Application\Catalog\Mapper;
use App\Application\Catalog\DTO\Response\PositionResponseDto; use App\Domain\Catalog\Entity\Position;
final class PositionResponseMapper { public function map(Position $position): PositionResponseDto { return new PositionResponseDto($position->id, $position->examId, $position->name, $position->emphasis); } }
