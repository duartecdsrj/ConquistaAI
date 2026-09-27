<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\QuestionBank;

use App\Domain\QuestionBank\Repository\QuestionAuditRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineQuestionAuditRepository implements QuestionAuditRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function start(string $algorithmVersion, string $scope): string
    {
        $run = new Entity\QuestionAuditRunRecord();
        $run->id = $this->id(); $run->algorithmVersion = $algorithmVersion; $run->scope = $scope;
        $run->status = 'PROCESSING'; $run->summary = []; $run->createdAt = new \DateTimeImmutable('now'); $run->startedAt = $run->createdAt;
        $this->em->persist($run); $this->em->flush();
        return $run->id;
    }

    public function findCompletedRunId(string $algorithmVersion, string $scope): ?string {
        $id = $this->em->createQueryBuilder()->select('run.id')->from(Entity\QuestionAuditRunRecord::class, 'run')->where('run.algorithmVersion = :version')->andWhere('run.scope = :scope')->andWhere('run.status = :status')->setParameter('version', $algorithmVersion)->setParameter('scope', $scope)->setParameter('status', 'COMPLETED')->orderBy('run.finishedAt', 'DESC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
        return is_array($id) && is_string($id['id'] ?? null) ? $id['id'] : null;
    }

    public function completedRunFinishedAt(string $algorithmVersion, string $scope): ?\DateTimeImmutable {
        $run = $this->em->createQueryBuilder()->select('run')->from(Entity\QuestionAuditRunRecord::class, 'run')->where('run.algorithmVersion = :version')->andWhere('run.scope = :scope')->andWhere('run.status = :status')->setParameter('version', $algorithmVersion)->setParameter('scope', $scope)->setParameter('status', 'COMPLETED')->orderBy('run.finishedAt', 'DESC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
        return $run instanceof Entity\QuestionAuditRunRecord ? $run->finishedAt : null;
    }

    public function record(string $runId, string $questionId, ?string $sourcePdfJobId, ?int $sourcePage, array $finding, array $before, array $after = []): void
    {
        $item = new Entity\QuestionAuditFindingRecord();
        $item->id = $this->id(); $item->auditRunId = $runId; $item->questionId = $questionId; $item->sourcePdfJobId = $sourcePdfJobId; $item->sourcePage = $sourcePage;
        $item->code = $finding['code']; $item->confidence = $finding['confidence']; $item->message = $finding['message']; $item->structureBefore = $before; $item->structureAfter = $after === [] ? null : $after;
        $item->status = match ($item->code) { 'IMAGEM_NAO_LOCALIZADA' => 'IMAGEM_NAO_LOCALIZADA', 'POSSIVEL_QUESTAO_MESCLADA' => 'POSSIVEL_QUESTAO_MESCLADA', 'POSSIVEL_DUPLICATA' => 'POSSIVEL_DUPLICATA', 'EXTRACAO_INCOMPLETA' => 'EXTRACAO_INCOMPLETA', default => 'REQUER_REVISAO' };
        $item->createdAt = new \DateTimeImmutable('now');
        $this->em->persist($item);
    }




    public function complete(string $runId, array $summary): void { $this->finish($runId, 'COMPLETED', $summary, null); }
    public function fail(string $runId, string $message): void { $this->finish($runId, 'FAILED', [], mb_substr($message, 0, 500)); }
    public function latest(): ?\App\Domain\QuestionBank\ReadModel\QuestionAuditReport {
        $run = $this->em->createQueryBuilder()->select('run')->from(Entity\QuestionAuditRunRecord::class, 'run')->orderBy('run.createdAt', 'DESC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if (!$run instanceof Entity\QuestionAuditRunRecord) return null;
        return new \App\Domain\QuestionBank\ReadModel\QuestionAuditReport($run->id, $run->algorithmVersion, $run->scope, $run->status, $run->summary, $run->createdAt->format(DATE_ATOM), $run->startedAt?->format(DATE_ATOM), $run->finishedAt?->format(DATE_ATOM), $run->errorMessage);
    }
    public function latestFindings(int $page, int $perPage): ?\App\Domain\QuestionBank\ReadModel\QuestionAuditFindingPage {
        $report = $this->latest(); if ($report === null) return null;
        $records = $this->em->createQueryBuilder()->select('finding')->from(Entity\QuestionAuditFindingRecord::class, 'finding')->where('finding.auditRunId = :run')->setParameter('run', $report->id)->orderBy('finding.createdAt', 'DESC')->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage)->getQuery()->getResult();
        $total = (int) $this->em->createQueryBuilder()->select('COUNT(finding.id)')->from(Entity\QuestionAuditFindingRecord::class, 'finding')->where('finding.auditRunId = :run')->setParameter('run', $report->id)->getQuery()->getSingleScalarResult();
        $items = array_map(static fn(Entity\QuestionAuditFindingRecord $finding): \App\Domain\QuestionBank\ReadModel\QuestionAuditFinding => new \App\Domain\QuestionBank\ReadModel\QuestionAuditFinding($finding->id, $finding->questionId, $finding->sourcePdfJobId, $finding->sourcePage, $finding->code, $finding->confidence, $finding->status, $finding->message, $finding->createdAt->format(DATE_ATOM), $finding->structureAfter), $records);
        return new \App\Domain\QuestionBank\ReadModel\QuestionAuditFindingPage($items, $page, $perPage, $total);
    }

    private function finish(string $id, string $status, array $summary, ?string $error): void { $run = $this->em->find(Entity\QuestionAuditRunRecord::class, $id); if (!$run instanceof Entity\QuestionAuditRunRecord) return; $run->status = $status; $run->summary = $summary; $run->errorMessage = $error; $run->finishedAt = new \DateTimeImmutable('now'); $this->em->flush(); }
    private function id(): string { $b = random_bytes(16); $b[6] = chr((ord($b[6]) & 15) | 64); $b[8] = chr((ord($b[8]) & 63) | 128); return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4)); }
}
