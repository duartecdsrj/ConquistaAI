<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Mapper;
use App\Application\QuestionBank\DTO\Response\QuestionCorrectionRequestResponseDto;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\Entity\QuestionCorrectionRequestRecord;
final class QuestionCorrectionRequestResponseMapper { public function toResponse(QuestionCorrectionRequestRecord $r,bool $includeProposal):QuestionCorrectionRequestResponseDto{return new QuestionCorrectionRequestResponseDto($r->id,$r->questionId,$r->status,$r->instruction,$r->createdAt->format(DATE_ATOM),$r->errorMessage,$includeProposal?$r->proposal:null);} }
