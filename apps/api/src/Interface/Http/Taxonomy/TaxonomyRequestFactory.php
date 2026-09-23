<?php
declare(strict_types=1);
namespace App\Interface\Http\Taxonomy;
use App\Application\Taxonomy\DTO\Request\CreateTaxonomySubjectAliasRequestDto;
use App\Application\Taxonomy\DTO\Request\CreateTaxonomySubjectRequestDto;
use App\Application\Taxonomy\DTO\Request\ListTaxonomySubjectsRequestDto;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
final class TaxonomyRequestFactory {
    public function list(ServerRequestInterface $request): ListTaxonomySubjectsRequestDto { $query=$request->getQueryParams(); $page=$query['page']??1; $perPage=$query['per_page']??25; if((!is_int($page)&&!(is_string($page)&&ctype_digit($page)))||(!is_int($perPage)&&!(is_string($perPage)&&ctype_digit($perPage)))) throw new InvalidArgumentException('Paginacao invalida.'); return new ListTaxonomySubjectsRequestDto((int)$page,(int)$perPage); }
    public function create(ServerRequestInterface $request): CreateTaxonomySubjectRequestDto { $payload=$this->payload($request); if(!is_string($payload['name']??null)||(($payload['parent_id']??null)!==null&&!is_string($payload['parent_id']))||(($payload['description']??null)!==null&&!is_string($payload['description']))) throw new InvalidArgumentException('Campos de assunto invalidos.'); return new CreateTaxonomySubjectRequestDto($payload['name'],$payload['parent_id']??null,$payload['description']??null); }
    public function createAlias(ServerRequestInterface $request): CreateTaxonomySubjectAliasRequestDto { $payload=$this->payload($request); if(!is_string($payload['subject_id']??null)||!is_string($payload['alias']??null)) throw new InvalidArgumentException('Campos de alias invalidos.'); return new CreateTaxonomySubjectAliasRequestDto($payload['subject_id'],$payload['alias']); }
    private function payload(ServerRequestInterface $request): array { try{$payload=json_decode((string)$request->getBody(),true,512,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new InvalidArgumentException('JSON invalido.');}if(!is_array($payload)||array_is_list($payload))throw new InvalidArgumentException('Campos invalidos.');return $payload; }
}
