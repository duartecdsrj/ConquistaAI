<?php
declare(strict_types=1);

namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Request\CreateSubjectRequestDto;
use App\Application\Catalog\DTO\Response\SubjectResponseDto;
use App\Application\Catalog\Mapper\SubjectResponseMapper;
use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Domain\Catalog\Entity\Subject;
use App\Domain\Catalog\Repository\SubjectRepositoryInterface;
use App\Domain\Catalog\Repository\SyllabusRepositoryInterface;

final class CreateSubjectService
{
    public function __construct(
        private readonly SubjectRepositoryInterface $subjects,
        private readonly SyllabusRepositoryInterface $syllabi,
        private readonly SubjectResponseMapper $mapper,
        private readonly TransactionManagerInterface $transactions,
    ) {}

    public function create(CreateSubjectRequestDto $input): SubjectResponseDto
    {
        $name = trim($input->name);
        if (!$this->syllabi->existsById($input->syllabusId) || ($input->parentId !== null && !$this->subjects->existsForSyllabus($input->parentId, $input->syllabusId))) { throw new \DomainException('Referencia de assunto invalida.'); }
        if ($name === '' || mb_strlen($name) > 190 || $input->sortOrder < 0) {
            throw new \InvalidArgumentException('Dados de assunto invalidos.');
        }

        $subject = new Subject($this->id(), $input->syllabusId, $input->parentId, $name, $input->sortOrder);
        $this->transactions->transactional(fn () => $this->subjects->save($subject));

        return $this->mapper->map($subject);
    }

    private function id(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
