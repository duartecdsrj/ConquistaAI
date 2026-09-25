<?php
declare(strict_types=1);
namespace Tests\Unit\Performance;
use App\Application\Performance\Service\GetStudyPlanService;
use App\Domain\Performance\Repository\PerformanceStatisticsRepositoryInterface;
use App\Domain\Performance\ValueObject\CompletedAnswer;
use App\Domain\Performance\ValueObject\StudyPlanAnswer;
use App\Domain\Performance\ValueObject\SyllabusCompletedAnswer;
use App\Domain\Performance\ValueObject\SyllabusOption;
use App\Domain\Performance\ValueObject\TaxonomyHierarchyNode;
use PHPUnit\Framework\TestCase;
final class GetStudyPlanServiceTest extends TestCase {
 public function testPrioritizesWeakSubjectsWithSufficientEvidence(): void {
  $repository = new class implements PerformanceStatisticsRepositoryInterface {
   public function completedAnswersForUser(string $userId): array { return []; }
   public function syllabiWithCompletedAnswersForUser(string $userId): array { return []; }
   public function completedAnswersForUserAndSyllabus(string $userId,string $syllabusId): array { return []; }
   public function completedAnswersForUserAndExam(string $userId,string $examId): array { return []; }
   public function taxonomyHierarchyForSyllabus(string $syllabusId): array { return []; }
   public function completedPlanAnswersForUser(string $userId): array { $answers=[]; for($i=0;$i<10;$i++) $answers[]=new StudyPlanAnswer('weak',$i<3,new \DateTimeImmutable('2026-09-'.(20+($i%3)))); return $answers; }
  };
  $plan=(new GetStudyPlanService($repository))->getForUser('user');
  self::assertCount(1,$plan->priorities);self::assertSame('weak',$plan->priorities[0]['subjectId']);self::assertSame(30.0,$plan->priorities[0]['percentage']);self::assertTrue($plan->priorities[0]['sufficientData']);
 }
}
