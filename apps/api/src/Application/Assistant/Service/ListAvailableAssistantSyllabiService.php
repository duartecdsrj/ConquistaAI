<?php
declare(strict_types=1);
namespace App\Application\Assistant\Service;
use App\Application\Assistant\DTO\Response\AvailableAssistantSyllabusResponseDto;use App\Domain\Assistant\Repository\SyllabusEvidenceRetrieverInterface;
final class ListAvailableAssistantSyllabiService { public function __construct(private readonly SyllabusEvidenceRetrieverInterface $evidence){} /** @return list<AvailableAssistantSyllabusResponseDto> */ public function list():array{return array_map(static fn($s)=>new AvailableAssistantSyllabusResponseDto($s->id,$s->name),$this->evidence->listAvailable());} }
