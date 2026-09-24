<?php
declare(strict_types=1);
namespace App\Application\Performance\DTO\Response;
final readonly class StudyPlanResponseDto { /** @param list<array{subjectId:string,total:int,correct:int,percentage:float,distinctDays:int,sufficientData:bool,reason:string,action:string}> $priorities */ public function __construct(public array $priorities) {} }
