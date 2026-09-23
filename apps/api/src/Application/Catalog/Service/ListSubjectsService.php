<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Response\SubjectResponseDto;
use App\Application\Catalog\Mapper\SubjectResponseMapper;
use App\Domain\Catalog\Repository\SubjectRepositoryInterface;

final class ListSubjectsService
{
    public function __construct(
        private readonly SubjectRepositoryInterface $subjects,
        private readonly SubjectResponseMapper $mapper,
    ) {}

    /** @return list<SubjectResponseDto> */
    public function list(string $syllabusId): array
    {
        return array_map($this->mapper->map(...), $this->subjects->listForSyllabus($syllabusId));
    }
}
