<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Response\ExamResponseDto;
use App\Application\Catalog\Mapper\ExamResponseMapper;
use App\Domain\Catalog\Repository\ExamRepositoryInterface;

final class ListExamsService
{
    public function __construct(private readonly ExamRepositoryInterface $exams, private readonly ExamResponseMapper $mapper)
    {
    }

    /** @return list<ExamResponseDto> */
    public function list(): array
    {
        return array_map($this->mapper->map(...), $this->exams->list());
    }
}
