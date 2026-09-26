<?php
declare(strict_types=1);
namespace App\Application\Study\DTO\Response;
final readonly class DirectedStudyPlanResponseDto { public function __construct(public string $id,public string $examId,public string $positionId,public string $name,public string $createdAt){} }
