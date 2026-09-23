<?php
declare(strict_types=1);

namespace Tests\Unit\Performance;

use App\Domain\Performance\Entity\Answer;
use PHPUnit\Framework\TestCase;

final class AnswerTest extends TestCase
{
    public function testItRejectsInvalidImmutableAnswerMetadata(): void
    {
        $this->expectException(\DomainException::class);
        new Answer('id', 'attempt', 'option', 0, 0, new \DateTimeImmutable());
    }

    public function testItKeepsAnswerFieldsReadOnly(): void
    {
        $answer = new Answer('id', 'attempt', 'option', 1, 12, new \DateTimeImmutable());
        self::assertSame('option', $answer->optionId);
        self::assertSame(12, $answer->elapsedSeconds);
    }
}
