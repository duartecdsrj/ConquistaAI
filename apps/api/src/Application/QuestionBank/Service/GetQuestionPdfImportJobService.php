<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Application\QuestionBank\DTO\Response\QuestionPdfImportJobResponseDto;use App\Application\QuestionBank\Mapper\QuestionPdfImportJobResponseMapper;use App\Domain\QuestionBank\Repository\QuestionPdfImportJobRepositoryInterface;
final class GetQuestionPdfImportJobService { public function __construct(private readonly QuestionPdfImportJobRepositoryInterface $jobs,private readonly QuestionPdfImportJobResponseMapper $mapper){} public function get(string $id,string $userId):?QuestionPdfImportJobResponseDto{$job=$this->jobs->findByIdForUser($id,$userId);return $job===null?null:$this->mapper->map($job);} }
