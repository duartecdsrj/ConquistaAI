<?php
declare(strict_types=1);
namespace App\Application\Review\DTO\Response;
final readonly class CobitGameResponseDto { /** @param list<array{id:string,code:string,name:string}> $domains @param list<array{id:string,code:string,name:string}> $processes */ public function __construct(public string $title,public string $instruction,public array $domains,public array $processes){} }
