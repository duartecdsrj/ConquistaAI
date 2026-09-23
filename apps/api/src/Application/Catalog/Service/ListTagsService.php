<?php
declare(strict_types=1);
namespace App\Application\Catalog\Service;
use App\Application\Catalog\DTO\Response\TagResponseDto;use App\Application\Catalog\Mapper\TagResponseMapper;use App\Domain\Catalog\Repository\TagRepositoryInterface;
final class ListTagsService {public function __construct(private readonly TagRepositoryInterface $tags,private readonly TagResponseMapper $mapper){} /** @return list<TagResponseDto> */ public function list():array{return array_map($this->mapper->map(...),$this->tags->list());}}
