<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Mapper;
use App\Application\QuestionBank\DTO\Response\QuestionAuditFindingResponseDto;
use App\Domain\QuestionBank\ReadModel\QuestionAuditFinding;
final class QuestionAuditFindingResponseMapper {
    public function map(QuestionAuditFinding $finding): QuestionAuditFindingResponseDto {
        return new QuestionAuditFindingResponseDto($finding->id, $finding->questionId, $finding->sourcePdfJobId, $finding->sourcePage, $finding->code, $finding->confidence, $finding->status, $finding->message, $finding->createdAt, $finding->structureAfter);
    }
}
