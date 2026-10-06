<?php
declare(strict_types=1);
namespace App\Application\Review\DTO\Request;
final readonly class CobitGameAnswersRequestDto { /** @param list<array{processId:string,domainId:string}> $answers */ public function __construct(public array $answers){} }
