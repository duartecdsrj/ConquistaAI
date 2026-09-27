<?php
declare(strict_types=1);

namespace App\Application\QuestionBank\Service;

use App\Domain\QuestionBank\Repository\PublishedQuestionRepositoryInterface;
use App\Domain\QuestionBank\Repository\QuestionAuditRepositoryInterface;
use App\Domain\QuestionBank\ReadModel\PublishedQuestionFilter;
use App\Domain\QuestionBank\Repository\PublishedQuestionAuditChangeDetectorInterface;
use App\Domain\QuestionBank\Service\QuestionAuditAnalyzer;

final class ProcessQuestionAuditService
{
    private const ALGORITHM_VERSION = 'question-audit-v4';
    public function __construct(private readonly PublishedQuestionRepositoryInterface $questions, private readonly QuestionAuditRepositoryInterface $audit, private readonly QuestionAuditAnalyzer $analyzer) {}


    public function run(): string
    {
        $existingRunId = $this->audit->findCompletedRunId(self::ALGORITHM_VERSION, 'PUBLISHED');
        $finishedAt = $this->audit->completedRunFinishedAt(self::ALGORITHM_VERSION, 'PUBLISHED');
        if ($existingRunId !== null && (!$this->questions instanceof PublishedQuestionAuditChangeDetectorInterface || $finishedAt === null || !$this->questions->hasAuditRelevantChangesSince($finishedAt))) return $existingRunId;

        $runId = $this->audit->start(self::ALGORITHM_VERSION, 'PUBLISHED'); $offset = 0; $summary = ['questions_analyzed' => 0, 'findings' => 0, 'requires_review' => 0];
        try {
            do {
                $page = $this->questions->findPublished(new PublishedQuestionFilter($offset, 100, null, null, null, null));
                foreach ($page->items as $question) $this->inspect($runId, $question, $summary);
                $offset += count($page->items);
            } while ($offset < $page->total && $page->items !== []);
            $this->audit->complete($runId, $summary); return $runId;
        } catch (\Throwable $error) { error_log('Question audit ' . $runId . ': ' . $error::class . ' ' . $error->getMessage()); $this->audit->fail($runId, $error->getMessage()); throw $error; }
    }
    private function inspect(string $runId, object $question, array &$summary): void
    {
        $summary['questions_analyzed']++;
        $options = array_map(static fn ($option): string => $option->content, $question->options);
        $before = ['statement' => $question->statement, 'options' => $options];
        foreach ($this->analyzer->inspect($question->statement, $options, count($question->assetUrls)) as $finding) {
            $this->audit->record($runId, $question->id, $question->sourcePdfJobId, $question->sourcePdfPages[0] ?? null, $finding, $before, $this->presentationAfter($finding)); $summary['findings']++;
            if ($finding['confidence'] !== 'HIGH') $summary['requires_review']++;
        }
    }
    /** @param array{code:string,confidence:string,message:string} $finding @return array<string,string> */
    private function presentationAfter(array $finding): array
    {
        return match ($finding['code']) {
            'CODIGO_SEM_BLOCO' => ['rendering' => 'CODE_BLOCK'],
            'ESTRUTURA_CORRELACAO' => ['rendering' => 'MATCHING_COLUMNS'],
            default => [],
        };
    }

}
