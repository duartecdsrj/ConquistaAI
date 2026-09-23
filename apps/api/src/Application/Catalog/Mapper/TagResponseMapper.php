<?php
declare(strict_types=1);
namespace App\Application\Catalog\Mapper;
use App\Application\Catalog\DTO\Response\TagResponseDto;use App\Domain\Catalog\Entity\Tag;
final class TagResponseMapper {public function map(Tag $tag):TagResponseDto{return new TagResponseDto($tag->id,$tag->name);}}
