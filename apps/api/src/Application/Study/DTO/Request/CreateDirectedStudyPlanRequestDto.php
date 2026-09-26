<?php
declare(strict_types=1);
namespace App\Application\Study\DTO\Request;
final readonly class CreateDirectedStudyPlanRequestDto { public function __construct(public string $userId,public string $examId,public string $positionId,public string $name){} }
