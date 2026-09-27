<?php
declare(strict_types=1);
namespace App\Interface\Http\QuestionBank;
use App\Application\QuestionBank\DTO\Request\CreateQuestionCorrectionRequestDto;
use Psr\Http\Message\ServerRequestInterface;
final class QuestionCorrectionRequestFactory { public function create(ServerRequestInterface $request,string $questionId,string $userId):CreateQuestionCorrectionRequestDto { try{$body=json_decode((string)$request->getBody(),true,512,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new \InvalidArgumentException('Envie um JSON válido.');}if(!is_array($body)||!is_string($body['instruction']??null))throw new \InvalidArgumentException('Informe a descrição da correção.');return new CreateQuestionCorrectionRequestDto($questionId,$userId,$body['instruction']);} }
