<?php
declare(strict_types=1);
namespace App\Application\Review\DTO\Response;
final readonly class CobitGameResultResponseDto { /** @param list<array{processId:string,correct:bool,expectedDomainId:string}> $answers */ public function __construct(public int $correctCount,public int $total,public int $percentage,public array $answers){} }
