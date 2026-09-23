<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Request\CreateExamRequestDto;
use App\Application\Catalog\DTO\Response\ExamResponseDto;
use App\Application\Catalog\Mapper\ExamResponseMapper;
use App\Domain\Catalog\Entity\Exam;
use App\Domain\Catalog\Repository\ExamRepositoryInterface;
use App\Application\Catalog\Port\TransactionManagerInterface;

final class CreateExamService
{
    public function __construct(
        private readonly ExamRepositoryInterface $exams,
        private readonly ExamResponseMapper $mapper,
        private readonly TransactionManagerInterface $transactions,
    ) {
    }

    public function create(CreateExamRequestDto $request): ExamResponseDto
    {
        $name = trim($request->name);
        $organizer = $request->organizer === null ? null : trim($request->organizer);
        $year = $request->year;
        $maximumYear = (int) (new \DateTimeImmutable('now'))->format('Y') + 1;

        if ($name === '' || mb_strlen($name) > 190 || ($organizer !== null && ($organizer === '' || mb_strlen($organizer) > 190)) || ($year !== null && ($year < 1900 || $year > $maximumYear))) {
            throw new \InvalidArgumentException('Dados do concurso invalidos.');
        }

        $exam = new Exam($this->uuid(), $name, $organizer, $year);
        $this->transactions->transactional(function () use ($exam): void { $this->exams->save($exam); });

        return $this->mapper->map($exam);
    }

    private function uuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
