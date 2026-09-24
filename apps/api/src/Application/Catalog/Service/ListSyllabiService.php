<?php
declare(strict_types=1);
namespace App\Application\Catalog\Service;
use App\Application\Catalog\DTO\Response\SyllabusResponseDto; use App\Application\Catalog\Mapper\SyllabusResponseMapper; use App\Domain\Catalog\Repository\SyllabusRepositoryInterface;
final class ListSyllabiService { public function __construct(private readonly SyllabusRepositoryInterface $syllabi,private readonly SyllabusResponseMapper $mapper){} /** @return list<SyllabusResponseDto> */ public function list(string $examId):array{return array_map($this->mapper->map(...),$this->syllabi->listForExam($examId));} }
