<?php
declare(strict_types=1);

namespace App\Application\Performance\Service;

use App\Application\Performance\DTO\Request\SaveStudyMapScheduleRequestDto;
use App\Application\Performance\Port\TransactionManagerInterface;
use App\Domain\Performance\Entity\StudyMapScheduleItem;
use App\Domain\Performance\Enum\StudyScheduleStatus;
use App\Domain\Performance\Repository\StudyMapScheduleRepositoryInterface;
use App\Domain\Performance\Repository\StudyMapSubjectScopeRepositoryInterface;

final readonly class SaveStudyMapScheduleService
{
    public function __construct(
        private StudyMapScheduleRepositoryInterface $schedule,
        private StudyMapSubjectScopeRepositoryInterface $scope,
        private TransactionManagerInterface $transactions,
    ) {}

    public function execute(SaveStudyMapScheduleRequestDto $request, \DateTimeImmutable $now): StudyMapScheduleItem
    {
        return $this->transactions->transactional(function () use ($request, $now): StudyMapScheduleItem {
            if ($request->endDate < $request->startDate) {
                throw new \InvalidArgumentException('A data final não pode ser anterior à inicial.');
            }
            if (!$this->scope->containsSubject($request->examId, $request->subjectId)) {
                throw new \InvalidArgumentException('O assunto não pertence ao concurso informado.');
            }
            $items = $this->schedule->listForUserExam($request->userId, $request->examId);
            $bySubject = [];
            foreach ($items as $item) {
                $bySubject[$item->taxonomySubjectId] = $item;
            }
            foreach (array_values(array_unique($request->predecessorSubjectIds)) as $predecessor) {
                if ($predecessor === $request->subjectId || !isset($bySubject[$predecessor])) {
                    throw new \InvalidArgumentException('A dependência deve apontar para um assunto já planejado no mesmo concurso.');
                }
            }
            $previous = $bySubject[$request->subjectId] ?? null;
            $bySubject[$request->subjectId] = new StudyMapScheduleItem($request->userId, $request->examId, $request->subjectId, $request->startDate, $request->endDate, $request->status, null, array_values(array_unique($request->predecessorSubjectIds)), $previous?->createdAt ?? $now, $now);
            if ($this->hasCycle($bySubject, $request->subjectId)) {
                throw new \InvalidArgumentException('As dependências do cronograma não podem formar ciclos.');
            }
            $completedAt = $request->status === StudyScheduleStatus::PLANNED ? null : ($previous?->completedAt ?? $now);
            $item = new StudyMapScheduleItem($request->userId, $request->examId, $request->subjectId, $request->startDate, $request->endDate, $request->status, $completedAt, array_values(array_unique($request->predecessorSubjectIds)), $previous?->createdAt ?? $now, $now);
            $this->schedule->save($item);
            return $item;
        });
    }

    /** @param array<string, StudyMapScheduleItem> $items */
    private function hasCycle(array $items, string $start): bool
    {
        $visiting = [];
        $visited = [];
        $visit = function (string $subjectId) use (&$visit, &$visiting, &$visited, $items): bool {
            if (isset($visiting[$subjectId])) return true;
            if (isset($visited[$subjectId])) return false;
            $visiting[$subjectId] = true;
            foreach ($items[$subjectId]->predecessorSubjectIds ?? [] as $predecessor) {
                if (isset($items[$predecessor]) && $visit($predecessor)) return true;
            }
            unset($visiting[$subjectId]);
            $visited[$subjectId] = true;
            return false;
        };
        return $visit($start);
    }
}
