<?php
declare(strict_types=1);
namespace App\Application\Study\Mapper;
use App\Application\Study\DTO\Response\DirectedStudyPlanResponseDto;use App\Domain\Study\Entity\DirectedStudyPlan;
final class DirectedStudyPlanResponseMapper { public function toResponse(DirectedStudyPlan $plan):DirectedStudyPlanResponseDto{return new DirectedStudyPlanResponseDto($plan->id,$plan->examId,$plan->positionId,$plan->name,$plan->createdAt->format(DATE_ATOM));} }
