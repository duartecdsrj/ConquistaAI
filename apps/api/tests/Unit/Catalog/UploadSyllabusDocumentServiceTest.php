<?php
declare(strict_types=1);

namespace Tests\Unit\Catalog;

use App\Application\Catalog\DTO\Request\UploadSyllabusDocumentRequestDto;
use App\Application\Catalog\Port\SyllabusDocumentStorageInterface;
use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Application\Catalog\Service\UploadSyllabusDocumentService;
use App\Domain\Catalog\Entity\Syllabus;
use App\Domain\Catalog\Repository\SyllabusRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class UploadSyllabusDocumentServiceTest extends TestCase
{
    public function testStoresPdfHashAndMetadata(): void
    {
        $original = new Syllabus('syllabus-1', 'position-1', 'Edital', null, null);
        $repository = new class($original) implements SyllabusRepositoryInterface {
            public ?Syllabus $saved = null;
            public function __construct(private readonly Syllabus $syllabus) {}
            public function save(Syllabus $syllabus): void { $this->saved = $syllabus; }
            public function findById(string $id): ?Syllabus { return $id === $this->syllabus->id ? $this->syllabus : null; }
            public function existsById(string $id): bool { return $this->findById($id) !== null; }
            public function existsForPosition(string $id, string $positionId): bool { return false; }
            public function listForPosition(string $positionId): array { return []; }
        };
        $storage = new class implements SyllabusDocumentStorageInterface {
            public string $storedHash = '';
            public function store(string $sha256, string $originalName, string $contents): string { $this->storedHash = $sha256; return '/documents/' . $sha256 . '.pdf'; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };
        $contents = "%PDF-1.7\nexample";

        $result = (new UploadSyllabusDocumentService($repository, $storage, $transactions))
            ->upload(new UploadSyllabusDocumentRequestDto('syllabus-1', 'edital.pdf', 'application/pdf', $contents));

        self::assertSame(hash('sha256', $contents), $result->documentSha256);
        self::assertSame('/documents/' . hash('sha256', $contents) . '.pdf', $result->documentPath);
        self::assertSame('edital.pdf', $result->documentOriginalName);
        self::assertSame(strlen($contents), $result->documentSize);
        self::assertSame($result, $repository->saved);
        self::assertSame($result->documentSha256, $storage->storedHash);
    }

    public function testRejectsNonPdfPayload(): void
    {
        $repository = new class implements SyllabusRepositoryInterface {
            public function save(Syllabus $syllabus): void {}
            public function findById(string $id): ?Syllabus { return null; }
            public function existsById(string $id): bool { return false; }
            public function existsForPosition(string $id, string $positionId): bool { return false; }
            public function listForPosition(string $positionId): array { return []; }
        };
        $storage = new class implements SyllabusDocumentStorageInterface {
            public function store(string $sha256, string $originalName, string $contents): string { return '/never'; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $this->expectException(\InvalidArgumentException::class);
        (new UploadSyllabusDocumentService($repository, $storage, $transactions))
            ->upload(new UploadSyllabusDocumentRequestDto('syllabus-1', 'arquivo.txt', 'text/plain', 'not a PDF'));
    }
}
