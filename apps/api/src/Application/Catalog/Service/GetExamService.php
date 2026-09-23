<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Response\ExamResponseDto;
use App\Application\Catalog\Mapper\ExamResponseMapper;
use App\Domain\Catalog\Repository\ExamRepositoryInterface;

final class GetExamService
{
    public function __construct(
        private readonly ExamRepositoryInterface $exams,
        private readonly ExamResponseMapper $mapper,
    ) {
    }

    public function get(string $id): ?ExamResponseDto
    {
        $exam = $this->exams->findById($id);

        return $exam === null ? null : $this->mapper->map($exam);
    }
}
