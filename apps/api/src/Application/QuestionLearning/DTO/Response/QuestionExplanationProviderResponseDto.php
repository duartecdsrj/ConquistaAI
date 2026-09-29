<?php
declare(strict_types=1);
namespace App\Application\QuestionLearning\DTO\Response;
final readonly class QuestionExplanationProviderResponseDto { /** @param array<string,mixed> $payload */ public function __construct(public array $payload, public string $provider, public string $model, public ?int $tokenCount = null, public ?int $durationMilliseconds = null) {} }
