<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalysisResponseDto;
use App\Application\QuestionBank\Mapper\QuestionImportAnalysisResponseMapper;
use App\Domain\QuestionBank\Repository\QuestionImportAnalysisRepositoryInterface;
use App\Domain\QuestionBank\Repository\QuestionPdfImportJobRepositoryInterface;
final readonly class ListQuestionPdfImportAnalysesService { public function __construct(private QuestionPdfImportJobRepositoryInterface $jobs,private QuestionImportAnalysisRepositoryInterface $analyses,private QuestionImportAnalysisResponseMapper $mapper) {} /** @return list<QuestionImportAnalysisResponseDto>|null */ public function list(string $jobId,string $userId): ?array { if($this->jobs->findByIdForUser($jobId,$userId)===null)return null; return array_map($this->mapper->map(...),$this->analyses->listForJob($jobId)); } }
