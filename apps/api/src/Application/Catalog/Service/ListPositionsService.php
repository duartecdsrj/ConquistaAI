<?php
declare(strict_types=1);
namespace App\Application\Catalog\Service;
use App\Application\Catalog\DTO\Response\PositionResponseDto; use App\Application\Catalog\Mapper\PositionResponseMapper; use App\Domain\Catalog\Repository\PositionRepositoryInterface;
final class ListPositionsService { public function __construct(private readonly PositionRepositoryInterface $positions,private readonly PositionResponseMapper $mapper){} /** @return list<PositionResponseDto> */ public function list(string $examId):array{return array_map($this->mapper->map(...),$this->positions->listForExam($examId));} }
