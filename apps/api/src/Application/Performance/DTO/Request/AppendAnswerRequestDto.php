<?php
declare(strict_types=1);
namespace App\Application\Performance\DTO\Request;
final readonly class AppendAnswerRequestDto { public function __construct(public string $attemptId, public ?string $optionId, public int $sequence, public int $elapsedSeconds) {} }
