<?php
declare(strict_types=1);

namespace Tests\Unit\Performance;

use App\Application\Performance\DTO\Request\SaveStudyMapScheduleRequestDto;
use App\Application\Performance\Port\TransactionManagerInterface;
use App\Application\Performance\Service\SaveStudyMapScheduleService;
use App\Domain\Performance\Entity\StudyMapScheduleItem;
use App\Domain\Performance\Enum\StudyScheduleStatus;
use App\Domain\Performance\Repository\StudyMapScheduleRepositoryInterface;
use App\Domain\Performance\Repository\StudyMapSubjectScopeRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class SaveStudyMapScheduleServiceTest extends TestCase
{
    public function testKeepsDatesWhenMarkingAnItemCompleted(): void
    {
        $repository = new InMemoryScheduleRepository();
        $service = $this->service($repository);
        $first = new SaveStudyMapScheduleRequestDto('u', 'e', 'a', new \DateTimeImmutable('2026-10-10'), new \DateTimeImmutable('2026-10-12'), StudyScheduleStatus::PLANNED, []);
        $service->execute($first, new \DateTimeImmutable('2026-10-01T12:00:00Z'));
        $completed = $service->execute(new SaveStudyMapScheduleRequestDto('u', 'e', 'a', new \DateTimeImmutable('2026-10-10'), new \DateTimeImmutable('2026-10-12'), StudyScheduleStatus::COMPLETED, []), new \DateTimeImmutable('2026-10-02T12:00:00Z'));
        self::assertSame('2026-10-10', $completed->startDate->format('Y-m-d'));
        self::assertSame('2026-10-12', $completed->endDate->format('Y-m-d'));
        self::assertSame(StudyScheduleStatus::COMPLETED, $completed->status);
        self::assertNotNull($completed->completedAt);
    }

    public function testRejectsCyclicDependencies(): void
    {
        $repository = new InMemoryScheduleRepository();
        $service = $this->service($repository);
        $service->execute(new SaveStudyMapScheduleRequestDto('u', 'e', 'a', new \DateTimeImmutable('2026-10-10'), new \DateTimeImmutable('2026-10-11'), StudyScheduleStatus::PLANNED, []), new \DateTimeImmutable());
        $service->execute(new SaveStudyMapScheduleRequestDto('u', 'e', 'b', new \DateTimeImmutable('2026-10-12'), new \DateTimeImmutable('2026-10-13'), StudyScheduleStatus::PLANNED, ['a']), new \DateTimeImmutable());
        $this->expectException(\InvalidArgumentException::class);
        $service->execute(new SaveStudyMapScheduleRequestDto('u', 'e', 'a', new \DateTimeImmutable('2026-10-10'), new \DateTimeImmutable('2026-10-11'), StudyScheduleStatus::PLANNED, ['b']), new \DateTimeImmutable());
    }

    private function service(InMemoryScheduleRepository $repository): SaveStudyMapScheduleService
    {
        return new SaveStudyMapScheduleService($repository, new class implements StudyMapSubjectScopeRepositoryInterface { public function containsSubject(string $examId, string $subjectId): bool { return in_array($subjectId, ['a', 'b'], true); } }, new class implements TransactionManagerInterface { public function transactional(callable $callback): mixed { return $callback(); } });
    }
}

final class InMemoryScheduleRepository implements StudyMapScheduleRepositoryInterface
{
    /** @var array<string, StudyMapScheduleItem> */ private array $items = [];
    public function listForUserExam(string $userId, string $examId): array { return array_values($this->items); }
    public function save(StudyMapScheduleItem $item): void { $this->items[$item->taxonomySubjectId] = $item; }
}
