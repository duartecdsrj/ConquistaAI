<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Request\UpdateExamRequestDto;
use App\Application\Catalog\DTO\Response\ExamResponseDto;
use App\Application\Catalog\Mapper\ExamResponseMapper;
use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Domain\Catalog\Entity\Exam;
use App\Domain\Catalog\Repository\ExamRepositoryInterface;

final class UpdateExamService
{
    public function __construct(
        private readonly ExamRepositoryInterface $exams,
        private readonly ExamResponseMapper $mapper,
        private readonly TransactionManagerInterface $transactions,
    ) {
    }

    public function update(UpdateExamRequestDto $request): ?ExamResponseDto
    {
        $name = trim($request->name);
        $organizer = $request->organizer === null ? null : trim($request->organizer);
        $maximumYear = (int) (new \DateTimeImmutable('now'))->format('Y') + 1;

        if ($name === '' || mb_strlen($name) > 190 || ($organizer !== null && ($organizer === '' || mb_strlen($organizer) > 190)) || ($request->year !== null && ($request->year < 1900 || $request->year > $maximumYear))) {
            throw new \InvalidArgumentException('Dados do concurso invalidos.');
        }

        $exam = new Exam($request->id, $name, $organizer, $request->year);
        $updated = $this->transactions->transactional(fn (): bool => $this->exams->update($exam));

        return $updated ? $this->mapper->map($exam) : null;
    }
}
