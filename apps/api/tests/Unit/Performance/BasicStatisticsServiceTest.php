<?php
declare(strict_types=1);
namespace Tests\Unit\Performance;
use App\Application\Performance\Service\BasicStatisticsService;
use App\Domain\Performance\ValueObject\CompletedAnswer;
use PHPUnit\Framework\TestCase;
final class BasicStatisticsServiceTest extends TestCase {
 public function testCalculatesTotalsAndSubjectPerformance():void {$result=(new BasicStatisticsService())->calculate([new CompletedAnswer('subject',true,10),new CompletedAnswer('subject',false,20)]);self::assertSame(2,$result->total);self::assertSame(1,$result->correct);self::assertSame(50.0,$result->percentage);self::assertSame(15.0,$result->averageElapsedSeconds);self::assertSame(1,$result->subjects['subject']['incorrect']);}
}
