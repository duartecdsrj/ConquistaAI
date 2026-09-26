<?php
declare(strict_types=1);
namespace App\Application\Catalog\Service;

use App\Application\Catalog\DTO\Request\UploadSyllabusDocumentRequestDto;
use App\Application\Catalog\Port\SyllabusDocumentStorageInterface;
use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Domain\Catalog\Entity\Syllabus;
use App\Domain\Catalog\Entity\SyllabusProcessingJob;
use App\Domain\Catalog\Repository\SyllabusProcessingJobRepositoryInterface;
use App\Domain\Catalog\Repository\SyllabusRepositoryInterface;

final class UploadSyllabusDocumentService
{
    public function __construct(
        private readonly SyllabusRepositoryInterface $syllabi,
        private readonly SyllabusDocumentStorageInterface $storage,
        private readonly TransactionManagerInterface $transactions,
        private readonly ?SyllabusProcessingJobRepositoryInterface $jobs = null,
    ) {
    }

    public function upload(UploadSyllabusDocumentRequestDto $input): Syllabus
    {
        return $this->transactions->transactional(function () use ($input): Syllabus {
            if ($input->mimeType !== 'application/pdf' || !str_starts_with($input->contents, '%PDF-')) {
                throw new \InvalidArgumentException('Informe um PDF valido.');
            }

            $syllabus = $this->syllabi->findById($input->syllabusId);
            if ($syllabus === null) {
                throw new \DomainException('Edital nao encontrado.');
            }

            $hash = hash('sha256', $input->contents);
            $path = $this->storage->store($hash, $input->originalName, $input->contents);
            $updated = new Syllabus($syllabus->id, $syllabus->examId, $syllabus->name, $syllabus->publishedAt, $syllabus->sourceUrl, $path, $hash, $input->originalName, $input->mimeType, strlen($input->contents));
            $this->syllabi->save($updated);
            $this->queueProcessing($updated, $hash);

            return $updated;
        });
    }

    private function queueProcessing(Syllabus $syllabus, string $hash): void
    {
        if ($this->jobs === null) {
            return;
        }

        $latest = $this->jobs->findLatestForSyllabus($syllabus->id);
        if ($latest !== null && $latest->documentSha256 === $hash && in_array($latest->status, ['PENDING', 'PROCESSING', 'COMPLETED'], true)) {
            return;
        }

        $this->jobs->save(new SyllabusProcessingJob($this->id(), $syllabus->id, $hash, 'PENDING', 0, null, new \DateTimeImmutable('now')));
    }

    private function id(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 15) | 64);
        $bytes[8] = chr((ord($bytes[8]) & 63) | 128);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
