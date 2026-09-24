<?php
declare(strict_types=1);

namespace Tests\Unit\Catalog;

use App\Application\Catalog\DTO\Request\CreateSubjectRequestDto;
use App\Application\Catalog\Mapper\SubjectResponseMapper;
use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Application\Catalog\Service\CreateSubjectService;
use App\Domain\Catalog\Entity\Subject;
use App\Domain\Catalog\Entity\Syllabus;
use App\Domain\Catalog\Repository\SubjectRepositoryInterface;
use App\Domain\Catalog\Repository\SyllabusRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class CreateSubjectServiceTest extends TestCase
{
    public function testRejectsUnknownSyllabus(): void
    {
        $subjects = new class implements SubjectRepositoryInterface {
            public function save(Subject $subject): void {}
            public function exists(string $id): bool { return false; }
            public function existsForSyllabus(string $id, string $syllabusId): bool { return false; }
            public function listForSyllabus(string $syllabusId): array { return []; }
        };
        $syllabi = new class implements SyllabusRepositoryInterface {
            public function save(Syllabus $syllabus): void {}
            public function findById(string $id): ?Syllabus { return null; }
            public function existsById(string $id): bool { return false; }
            public function existsForExam(string $id, string $positionId): bool { return false; }
            public function listForExam(string $positionId): array { return []; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $this->expectException(\DomainException::class);
        (new CreateSubjectService($subjects, $syllabi, new SubjectResponseMapper(), $transactions))
            ->create(new CreateSubjectRequestDto('missing', null, 'Redes', 0));
    }

    public function testRejectsParentFromAnotherSyllabus(): void
    {
        $subjects = new class implements SubjectRepositoryInterface {
            public function save(Subject $subject): void {}
            public function exists(string $id): bool { return false; }
            public function existsForSyllabus(string $id, string $syllabusId): bool { return false; }
            public function listForSyllabus(string $syllabusId): array { return []; }
        };
        $syllabi = new class implements SyllabusRepositoryInterface {
            public function save(Syllabus $syllabus): void {}
            public function findById(string $id): ?Syllabus { return null; }
            public function existsById(string $id): bool { return true; }
            public function existsForExam(string $id, string $positionId): bool { return false; }
            public function listForExam(string $positionId): array { return []; }
        };
        $transactions = new class implements TransactionManagerInterface {
            public function transactional(callable $callback): mixed { return $callback(); }
        };

        $this->expectException(\DomainException::class);
        (new CreateSubjectService($subjects, $syllabi, new SubjectResponseMapper(), $transactions))
            ->create(new CreateSubjectRequestDto('s1', 'foreign', 'Redes', 0));
    }

    public function testPreservesSourceProvenance(): void
    {
        $subjects = new class implements SubjectRepositoryInterface {
            public ?Subject $saved = null;
            public function save(Subject $subject): void { $this->saved = $subject; }
            public function exists(string $id): bool { return false; }
            public function existsForSyllabus(string $id, string $syllabusId): bool { return false; }
            public function listForSyllabus(string $syllabusId): array { return []; }
        };
        $syllabi = new class implements SyllabusRepositoryInterface {
            public function save(Syllabus $syllabus): void {}
            public function findById(string $id): ?Syllabus { return null; }
            public function existsById(string $id): bool { return $id === 's1'; }
            public function existsForExam(string $id, string $positionId): bool { return false; }
            public function listForExam(string $positionId): array { return []; }
        };
        $transactions = new class implements TransactionManagerInterface { public function transactional(callable $callback): mixed { return $callback(); } };

        $result = (new CreateSubjectService($subjects, $syllabi, new SubjectResponseMapper(), $transactions))
            ->create(new CreateSubjectRequestDto('s1', null, 'TCP/IP', 0, 'Protocolos de rede TCP/IP.', 12, 50, 78));

        self::assertSame('Protocolos de rede TCP/IP.', $result->sourceExcerpt);
        self::assertSame(12, $result->sourcePage);
        self::assertSame(50, $result->sourceStartOffset);
        self::assertSame(78, $result->sourceEndOffset);
        self::assertSame('TCP/IP', $subjects->saved?->name);
    }
}
