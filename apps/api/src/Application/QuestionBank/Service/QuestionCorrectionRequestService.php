<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\Service;
use App\Application\QuestionBank\DTO\Request\CreateQuestionCorrectionRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionCorrectionRequestResponseDto;
use App\Application\QuestionBank\Mapper\QuestionCorrectionRequestResponseMapper;
use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionCorrectionRequestRepository;
final class QuestionCorrectionRequestService {
    public function __construct(private readonly DoctrineQuestionCorrectionRequestRepository $requests,private readonly QuestionCorrectionRequestResponseMapper $mapper){}
    public function request(CreateQuestionCorrectionRequestDto $input):QuestionCorrectionRequestResponseDto{$instruction=trim($input->instruction);if(mb_strlen($instruction)<3||mb_strlen($instruction)>2000)throw new \InvalidArgumentException('Descreva a correção entre 3 e 2000 caracteres.');$snapshot=$this->requests->questionSnapshot($input->questionId);if($snapshot===null)throw new \DomainException('Questão publicada não encontrada.');return $this->mapper->toResponse($this->requests->create($input->questionId,$input->requestedBy,$instruction,$snapshot),false);}
    public function latest(string $questionId,string $userId,bool $admin):?QuestionCorrectionRequestResponseDto{$r=$this->requests->latestVisible($questionId,$userId,$admin);return $r===null?null:$this->mapper->toResponse($r,$admin);}
    public function approve(string $id,string $adminId):QuestionCorrectionRequestResponseDto{$r=$this->requests->find($id);if($r===null)throw new \DomainException('Solicitação não encontrada.');$this->requests->approve($r,$adminId);return $this->mapper->toResponse($r,true);}
}
