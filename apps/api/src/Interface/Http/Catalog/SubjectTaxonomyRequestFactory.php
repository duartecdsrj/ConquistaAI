<?php
declare(strict_types=1);
namespace App\Interface\Http\Catalog;
use App\Application\Catalog\DTO\Request\AssignSubjectTaxonomySubjectsRequestDto;use InvalidArgumentException;use Psr\Http\Message\ServerRequestInterface;
final class SubjectTaxonomyRequestFactory { public function assign(ServerRequestInterface $request,string $subjectId):AssignSubjectTaxonomySubjectsRequestDto { try{$body=json_decode((string)$request->getBody(),true,512,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new InvalidArgumentException();}$ids=$body['taxonomy_subject_ids']??null;if(!is_array($body)||!is_array($ids)||array_filter($ids,static fn($value)=>!is_string($value)))throw new InvalidArgumentException();return new AssignSubjectTaxonomySubjectsRequestDto($subjectId,array_values($ids));} }
