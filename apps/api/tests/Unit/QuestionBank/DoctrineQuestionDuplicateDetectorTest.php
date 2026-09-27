<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionBank;

use App\Infrastructure\Persistence\Doctrine\QuestionBank\DoctrineQuestionDuplicateDetector;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class DoctrineQuestionDuplicateDetectorTest extends TestCase
{
    public function testNormalizesOnlyExtractionFormattingForExactDuplicateComparison(): void
    {
        $detector = new DoctrineQuestionDuplicateDetector($this->createMock(EntityManagerInterface::class));
        $method = new \ReflectionMethod($detector, 'normalize');

        self::assertSame($method->invoke($detector, 'A função é ﬁnal: valor = 10.'), $method->invoke($detector, "A FUNCAO\nE FINAL — valor 10"));
    }
}
