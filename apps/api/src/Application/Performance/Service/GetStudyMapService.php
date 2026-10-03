<?php
declare(strict_types=1);

namespace App\Application\Performance\Service;

use App\Application\Performance\DTO\Request\GetStudyMapRequestDto;
use App\Application\Performance\DTO\Response\StudyMapResponseDto;
use App\Application\Performance\DTO\Response\StudyMapScheduleResponseDto;
use App\Application\Performance\DTO\Response\StudyMapSubjectResponseDto;
use App\Domain\Performance\Repository\PerformanceStatisticsRepositoryInterface;
use App\Domain\Performance\Repository\StudyMapScheduleRepositoryInterface;
use App\Domain\Performance\ValueObject\TaxonomyHierarchyNode;

final readonly class GetStudyMapService
{
    public function __construct(private PerformanceStatisticsRepositoryInterface $statistics, private StudyMapScheduleRepositoryInterface $schedule) {}

    public function execute(GetStudyMapRequestDto $request): StudyMapResponseDto
    {
        $nodes = $this->statistics->taxonomyHierarchyForExam($request->examId);
        $byId = [];
        foreach ($nodes as $node) $byId[$node->id] = $node;
        $metrics = [];
        $answers = array_values(array_filter($this->statistics->completedAnswersForUserAndExam($request->userId, $request->examId), static fn ($answer): bool => ($request->from === null || $answer->completedAt >= $request->from) && ($request->to === null || $answer->completedAt <= $request->to->setTime(23, 59, 59))));
        foreach ($answers as $answer) {
            $visited = [];
            foreach ($answer->taxonomySubjectIds as $subjectId) {
                while (isset($byId[$subjectId]) && !isset($visited[$subjectId])) {
                    $visited[$subjectId] = true;
                    $metrics[$subjectId] ??= ['answered' => 0, 'correct' => 0, 'days' => []];
                    $metrics[$subjectId]['answered']++;
                    $metrics[$subjectId]['correct'] += $answer->isCorrect ? 1 : 0;
                    $metrics[$subjectId]['days'][$answer->completedAt->format('Y-m-d')] = true;
                    $subjectId = $byId[$subjectId]->parentId;
                }
            }
        }
        $children = [];
        foreach ($nodes as $node) if ($node->parentId !== null && isset($byId[$node->parentId])) $children[$node->parentId][] = $node;
        $roots = array_values(array_filter($nodes, static fn (TaxonomyHierarchyNode $node): bool => $node->parentId === null || !isset($byId[$node->parentId])));
        $subjects = array_map(fn (TaxonomyHierarchyNode $node): StudyMapSubjectResponseDto => $this->subject($node, $children, $metrics, 0), $roots);
        $correct = count(array_filter($answers, static fn ($answer): bool => $answer->isCorrect));
        $total = count($answers);
        return new StudyMapResponseDto($request->examId, $request->from?->format('Y-m-d'), $request->to?->format('Y-m-d'), $total, $correct, $total - $correct, $total ? round(($correct / $total) * 100, 2) : null, 0, $subjects, array_map(static fn ($item): StudyMapScheduleResponseDto => new StudyMapScheduleResponseDto($item->taxonomySubjectId, $item->startDate->format('Y-m-d'), $item->endDate->format('Y-m-d'), $item->status->value, $item->completedAt?->format(DATE_ATOM), $item->predecessorSubjectIds), $this->schedule->listForUserExam($request->userId, $request->examId)));
    }

    /** @param array<string, list<TaxonomyHierarchyNode>> $children @param array<string, array{answered:int,correct:int,days:array<string,bool>}> $metrics */
    private function subject(TaxonomyHierarchyNode $node, array $children, array $metrics, int $depth): StudyMapSubjectResponseDto
    {
        $metric = $metrics[$node->id] ?? ['answered' => 0, 'correct' => 0, 'days' => []];
        $answered = $metric['answered'];
        $accuracy = $answered ? round(($metric['correct'] / $answered) * 100, 2) : null;
        $status = $answered === 0 ? 'NO_DATA' : ($answered >= 10 && count($metric['days']) >= 3 ? 'SUFFICIENT' : 'INSUFFICIENT');
        return new StudyMapSubjectResponseDto($node->id, $node->parentId, $node->name, $depth, $answered, $metric['correct'], $answered - $metric['correct'], $accuracy, count($metric['days']), $status, array_map(fn (TaxonomyHierarchyNode $child): StudyMapSubjectResponseDto => $this->subject($child, $children, $metrics, $depth + 1), $children[$node->id] ?? []));
    }
}
