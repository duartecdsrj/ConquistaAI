<?php
declare(strict_types=1);
namespace App\Application\Study\Service;
use App\Application\Study\DTO\Response\StudyContestSubjectResponseDto;use App\Domain\Study\Repository\StudyContestSubjectRepositoryInterface;
final class ListStudyContestSubjectsService { public function __construct(private readonly StudyContestSubjectRepositoryInterface $subjects){} /** @return list<StudyContestSubjectResponseDto> */ public function list(string $positionId):array{return array_map(static fn($s)=>new StudyContestSubjectResponseDto($s->id,$s->parentId,$s->name,$s->level),$this->subjects->listForPosition($positionId));} }
