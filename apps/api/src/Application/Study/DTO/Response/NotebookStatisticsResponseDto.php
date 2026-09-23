<?php
declare(strict_types=1);
namespace App\Application\Study\DTO\Response;
final readonly class NotebookStatisticsResponseDto { /** @param list<string> $answeredQuestionIds */ public function __construct(public int $total,public int $answered,public int $correct,public int $incorrect,public float $percentage,public float $averageElapsedSeconds,public int $elapsedSeconds,public array $answeredQuestionIds) {} }
