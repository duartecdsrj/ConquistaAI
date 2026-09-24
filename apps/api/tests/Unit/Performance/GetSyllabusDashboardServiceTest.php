<?php
declare(strict_types=1);
namespace Tests\Unit\Performance;
use App\Application\Performance\DTO\Request\GetSyllabusDashboardRequestDto;
use App\Application\Performance\Service\GetSyllabusDashboardService;
use App\Domain\Performance\Repository\PerformanceStatisticsRepositoryInterface;
use App\Domain\Performance\ValueObject\CompletedAnswer;
use App\Domain\Performance\ValueObject\SyllabusCompletedAnswer;
use App\Domain\Performance\ValueObject\SyllabusOption;
use App\Domain\Performance\ValueObject\TaxonomyHierarchyNode;
use PHPUnit\Framework\TestCase;
final class GetSyllabusDashboardServiceTest extends TestCase {
 public function testAggregatesDescendantsAndReportsInsufficientData(): void {
  $repository = new class implements PerformanceStatisticsRepositoryInterface {
   public function completedAnswersForUser(string $userId): array { return []; }
   public function syllabiWithCompletedAnswersForUser(string $userId): array { return [new SyllabusOption('s','Edital','Cargo','Concurso')]; }
   public function completedAnswersForUserAndSyllabus(string $userId,string $syllabusId): array { return [new SyllabusCompletedAnswer(true,12,new \DateTimeImmutable('2026-09-20'),['child'])]; }
   public function taxonomyHierarchyForSyllabus(string $syllabusId): array { return [new TaxonomyHierarchyNode('root',null,'Raiz'),new TaxonomyHierarchyNode('child','root','Filho')]; }
  };
  $result=(new GetSyllabusDashboardService($repository))->getForUser('u',new GetSyllabusDashboardRequestDto('s'));
  self::assertSame(1,$result->total);self::assertFalse($result->sufficientData);self::assertSame('root',$result->subjects[1]['id']);self::assertSame(1,$result->subjects[1]['total']);
 }
}
