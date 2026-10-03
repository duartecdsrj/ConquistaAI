<?php
declare(strict_types=1);

namespace App\Interface\Http\Performance;

use App\Application\Performance\DTO\Request\SaveStudyMapScheduleRequestDto;
use App\Domain\Performance\Enum\StudyScheduleStatus;
use Psr\Http\Message\ServerRequestInterface;

final class StudyMapRequestFactory
{
    public function dates(array $query): array
    {
        $from = $this->date($query['from'] ?? null);
        $to = $this->date($query['to'] ?? null);
        if ($from !== null && $to !== null && $from > $to) throw new \InvalidArgumentException('Intervalo inválido.');
        return [$from, $to];
    }

    public function schedule(ServerRequestInterface $request, ?string $subjectId = null): SaveStudyMapScheduleRequestDto
    {
        $payload = json_decode((string) $request->getBody(), true);
        if (!is_array($payload)) throw new \InvalidArgumentException('JSON inválido.');
        $examId = $payload['examId'] ?? null;
        $resolvedSubjectId = $subjectId ?? ($payload['subjectId'] ?? null);
        $start = $this->date($payload['startDate'] ?? null);
        $end = $this->date($payload['endDate'] ?? null);
        $status = isset($payload['status']) && is_string($payload['status']) ? StudyScheduleStatus::tryFrom($payload['status']) : null;
        $predecessors = $payload['predecessorSubjectIds'] ?? [];
        if (!is_string($examId) || !is_string($resolvedSubjectId) || $start === null || $end === null || $status === null || !is_array($predecessors) || array_filter($predecessors, static fn ($id): bool => !is_string($id))) throw new \InvalidArgumentException('Dados de cronograma inválidos.');
        return new SaveStudyMapScheduleRequestDto('', $examId, $resolvedSubjectId, $start, $end, $status, array_values(array_unique($predecessors)));
    }

    private function date(mixed $value): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') return null;
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) throw new \InvalidArgumentException('Data inválida.');
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if (!$date instanceof \DateTimeImmutable || $date->format('Y-m-d') !== $value) throw new \InvalidArgumentException('Data inválida.');
        return $date;
    }
}
