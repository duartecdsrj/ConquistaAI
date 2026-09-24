<?php
declare(strict_types=1);
namespace App\Application\Performance\DTO\Response;
final readonly class SyllabusDashboardResponseDto { /** @param list<array{id:string,name:string,positionName:string,examName:string}> $syllabi @param list<array{id:string,parentId:?string,name:string,total:int,correct:int,incorrect:int,percentage:float,distinctDays:int,sufficientData:bool}> $subjects */ public function __construct(public array $syllabi, public ?string $selectedSyllabusId, public int $total, public int $correct, public int $incorrect, public float $percentage, public float $averageElapsedSeconds, public bool $sufficientData, public int $distinctDays, public array $subjects) {} }
