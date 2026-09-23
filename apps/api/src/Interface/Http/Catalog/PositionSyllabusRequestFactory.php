<?php
declare(strict_types=1);
namespace App\Interface\Http\Catalog;
use App\Application\Catalog\DTO\Request\CreatePositionRequestDto; use App\Application\Catalog\DTO\Request\CreateSyllabusRequestDto; use InvalidArgumentException; use Psr\Http\Message\ServerRequestInterface;
final class PositionSyllabusRequestFactory {
 public function position(ServerRequestInterface $r,string $examId):CreatePositionRequestDto{$p=$this->json($r);if(!is_string($p['name']??null)||(($p['emphasis']??null)!==null&&!is_string($p['emphasis'])))throw new InvalidArgumentException('Campos de cargo invalidos.');return new CreatePositionRequestDto($examId,$p['name'],$p['emphasis']??null);}
 public function syllabus(ServerRequestInterface $r,string $positionId):CreateSyllabusRequestDto{$p=$this->json($r);if(!is_string($p['name']??null)||(($p['published_at']??null)!==null&&!is_string($p['published_at']))||(($p['source_url']??null)!==null&&!is_string($p['source_url'])))throw new InvalidArgumentException('Campos de edital invalidos.');return new CreateSyllabusRequestDto($positionId,$p['name'],$p['published_at']??null,$p['source_url']??null);}
 private function json(ServerRequestInterface $r):array{try{$p=json_decode((string)$r->getBody(),true,512,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new InvalidArgumentException('JSON invalido.');}if(!is_array($p)||array_is_list($p))throw new InvalidArgumentException('O corpo deve ser um objeto JSON.');return $p;}
}
