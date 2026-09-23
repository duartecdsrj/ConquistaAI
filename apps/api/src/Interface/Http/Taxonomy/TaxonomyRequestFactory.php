<?php
declare(strict_types=1);
namespace App\Interface\Http\Taxonomy;
use App\Application\Taxonomy\DTO\Request\CreateTaxonomySubjectRequestDto;use InvalidArgumentException;use Psr\Http\Message\ServerRequestInterface;
final class TaxonomyRequestFactory { public function create(ServerRequestInterface $request):CreateTaxonomySubjectRequestDto {try{$payload=json_decode((string)$request->getBody(),true,512,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new InvalidArgumentException('JSON invalido.');}if(!is_array($payload)||array_is_list($payload)||!is_string($payload['name']??null)||(($payload['parent_id']??null)!==null&&!is_string($payload['parent_id']))||(($payload['description']??null)!==null&&!is_string($payload['description'])))throw new InvalidArgumentException('Campos de assunto invalidos.');return new CreateTaxonomySubjectRequestDto($payload['name'],$payload['parent_id']??null,$payload['description']??null);}}
