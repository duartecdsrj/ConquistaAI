<?php
declare(strict_types=1);
namespace App\Interface\Http\Catalog;
use App\Application\Catalog\DTO\Request\QueueSyllabusProcessingRequestDto;use Psr\Http\Message\ServerRequestInterface;
final class SyllabusProcessingRequestFactory { public function queue(ServerRequestInterface $request,string $syllabusId):QueueSyllabusProcessingRequestDto{$body=(string)$request->getBody();if($body==='')return new QueueSyllabusProcessingRequestDto($syllabusId);try{$payload=json_decode($body,true,512,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new \InvalidArgumentException();}if(!is_array($payload)||isset($payload['reprocess'])&&!is_bool($payload['reprocess']))throw new \InvalidArgumentException();return new QueueSyllabusProcessingRequestDto($syllabusId,$payload['reprocess']??false);} }
