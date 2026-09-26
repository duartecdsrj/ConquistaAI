<?php
declare(strict_types=1);

namespace App\Application\Study\Service;

use App\Application\Study\DTO\Request\CreateNotebookRequestDto;
use App\Application\Study\DTO\Response\NotebookResponseDto;
use App\Application\Study\Mapper\NotebookResponseMapper;
use App\Application\Study\Port\TransactionManagerInterface;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionFilter;
use App\Domain\QuestionBank\Repository\PublishedQuestionRepositoryInterface;
use App\Domain\Study\Entity\Notebook;
use App\Domain\Study\Enum\NotebookMode;
use App\Domain\Study\Repository\NotebookRepositoryInterface;
use App\Domain\Catalog\Repository\PositionRepositoryInterface;
use App\Domain\Catalog\Repository\PositionTaxonomyAssignmentRepositoryInterface;
use App\Domain\Study\Repository\StudyContestSubjectRepositoryInterface;
use App\Domain\Study\ValueObject\FrozenQuestionSelection;

final class CreateNotebookService
{
    public function __construct(
        private readonly NotebookRepositoryInterface $notebooks,
        private readonly PublishedQuestionRepositoryInterface $questions,
        private readonly NotebookResponseMapper $mapper,
        private readonly TransactionManagerInterface $transactions,
        private readonly ?PositionRepositoryInterface $positions = null,
        private readonly ?PositionTaxonomyAssignmentRepositoryInterface $positionSubjects = null,
        private readonly ?StudyContestSubjectRepositoryInterface $studySubjects = null,
        private readonly \DateTimeZone $utc = new \DateTimeZone('UTC'),
    ) {
    }

    public function create(CreateNotebookRequestDto $request): NotebookResponseDto
    {
        $selectedSubjects = [];
        $subjectWeights = [];
        if (($request->filters['exam_id'] ?? null) !== null || ($request->filters['position_id'] ?? null) !== null) {
            $examId = $request->filters['exam_id'] ?? null; $positionId = $request->filters['position_id'] ?? null;
            if (!is_string($examId) || !is_string($positionId) || $this->positions === null || !$this->positions->existsForExam($positionId, $examId)) throw new \InvalidArgumentException('Informe um concurso e um cargo válido desse concurso.');
            $available = $this->studySubjects?->listForPosition($positionId) ?? [];
            $allowedSubjectIds = array_map(static fn($subject): string => $subject->id, $available);
            $requested = isset($request->filters['subject_ids']) && is_array($request->filters['subject_ids']) ? array_values(array_unique(array_filter($request->filters['subject_ids'], 'is_string'))) : [];
            if ($requested !== [] && array_diff($requested, $allowedSubjectIds) !== []) throw new \InvalidArgumentException('Selecione somente assuntos associados ao cargo escolhido.');
            $selectedSubjects = $requested === [] ? $allowedSubjectIds : $requested;
            if ($selectedSubjects === []) throw new \InvalidArgumentException('O cargo escolhido ainda não possui assuntos associados.');
            foreach ($available as $subject) if (in_array($subject->id, $selectedSubjects, true)) $subjectWeights[$subject->id] = $subject->selectionWeight;
        }
        return $this->transactions->transactional(function () use ($request, $selectedSubjects, $subjectWeights): NotebookResponseDto {
            if ($selectedSubjects !== []) {
                $ids = $this->weightedQuestionIds($request, $selectedSubjects, $subjectWeights);
            } else {
                $page = $this->questions->findPublished(new PublishedQuestionFilter(0,$request->quantity,$request->filters['subject_id'] ?? null,$request->filters['board'] ?? null,$request->filters['year'] ?? null,$request->filters['difficulty'] ?? null,$request->filters['syllabus_id'] ?? null,isset($request->filters['subject_ids']) && is_array($request->filters['subject_ids']) ? array_values($request->filters['subject_ids']) : [],$request->filters['exam_id'] ?? null));
                $ids = array_map(static fn ($question): string => $question->id, $page->items);
            }
            $selection = FrozenQuestionSelection::fromQuestionIds($ids,$request->quantity);
            $notebook = Notebook::create($request->userId,$request->name,NotebookMode::from($request->mode),$selection,new \DateTimeImmutable('now', $this->utc),$request->filters);
            $this->notebooks->save($notebook);
            return $this->mapper->toResponse($notebook);
        });
    }

    /** @param list<string> $subjectIds @param array<string,float> $weights @return list<string> */
    private function weightedQuestionIds(CreateNotebookRequestDto $request, array $subjectIds, array $weights): array
    {
        $positive = array_map(static fn(string $id): float => max(0.0, $weights[$id] ?? 1.0), $subjectIds);
        $sum = array_sum($positive); if ($sum <= 0.0) { $positive = array_fill(0, count($subjectIds), 1.0); $sum = (float) count($subjectIds); }
        $quotas=[];$remainders=[];$allocated=0;
        foreach ($subjectIds as $index=>$id) { $exact=$request->quantity*$positive[$index]/$sum; $quotas[$id]=(int)floor($exact); $remainders[$id]=$exact-$quotas[$id]; $allocated+=$quotas[$id]; }
        uasort($remainders, static fn(float $a,float $b):int=>$b<=>$a);
        foreach (array_keys($remainders) as $id) { if ($allocated >= $request->quantity) break; $quotas[$id]++; $allocated++; }
        $ids=[];
        foreach ($subjectIds as $id) {
            $page=$this->questions->findPublished(new PublishedQuestionFilter(0,max($request->quantity,$quotas[$id]),null,$request->filters['board'] ?? null,$request->filters['year'] ?? null,$request->filters['difficulty'] ?? null,null,[$id],null));
            foreach ($page->items as $question) { if (count($ids) >= $request->quantity || count(array_filter($ids, static fn(string $chosen):bool=>$chosen===$question->id)) > 0) continue; if ($quotas[$id]-- <= 0) break; $ids[]=$question->id; }
        }
        if (count($ids) < $request->quantity) {
            $page=$this->questions->findPublished(new PublishedQuestionFilter(0,$request->quantity*3,null,$request->filters['board'] ?? null,$request->filters['year'] ?? null,$request->filters['difficulty'] ?? null,null,$subjectIds,null));
            foreach ($page->items as $question) { if (count($ids) >= $request->quantity) break; if (!in_array($question->id,$ids,true)) $ids[]=$question->id; }
        }
        return $ids;
    }

}
