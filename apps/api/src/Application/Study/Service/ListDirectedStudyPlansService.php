<?php
declare(strict_types=1);
namespace App\Application\Study\Service;
use App\Application\Study\DTO\Response\DirectedStudyPlanResponseDto;use App\Application\Study\Mapper\DirectedStudyPlanResponseMapper;use App\Domain\Study\Repository\DirectedStudyPlanRepositoryInterface;
final class ListDirectedStudyPlansService { public function __construct(private readonly DirectedStudyPlanRepositoryInterface $plans,private readonly DirectedStudyPlanResponseMapper $mapper){} /** @return list<DirectedStudyPlanResponseDto> */ public function list(string $userId):array{return array_map($this->mapper->toResponse(...),$this->plans->listForUser($userId));} }
