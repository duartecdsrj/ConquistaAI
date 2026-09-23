<?php
declare(strict_types=1);

namespace Tests\Unit\Catalog;

use App\Application\Catalog\DTO\Request\CreateExamRequestDto;
use App\Application\Catalog\Mapper\ExamResponseMapper;
use App\Application\Catalog\Port\TransactionManagerInterface;
use App\Application\Catalog\Service\CreateExamService;
use App\Domain\Catalog\Entity\Exam;
use App\Domain\Catalog\Repository\ExamRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class CreateExamServiceTest extends TestCase
{
    public function testItPersistsAValidExamInsideATransaction(): void
    {
        $repository = new InMemoryExams();
        $transactions = new CatalogTransactionSpy();
        $service = new CreateExamService($repository, new ExamResponseMapper(), $transactions);

        $response = $service->create(new CreateExamRequestDto('Concurso', 'Banca', 2026));

        self::assertSame('Concurso', $response->name);
        self::assertCount(1, $repository->saved);
        self::assertSame(1, $transactions->calls);
    }

    public function testItRejectsInvalidYearBeforePersistence(): void
    {
        $repository = new InMemoryExams();
        $service = new CreateExamService($repository, new ExamResponseMapper(), new CatalogTransactionSpy());

        $this->expectException(\InvalidArgumentException::class);
        $service->create(new CreateExamRequestDto('Concurso', null, 1800));
        self::assertCount(0, $repository->saved);
    }
}
final class InMemoryExams implements ExamRepositoryInterface
{
    /** @var list<Exam> */
    public array $saved = [];
    public function save(Exam $exam): void { $this->saved[] = $exam; }
    public function findById(string $id): ?Exam { return null; }
    public function list(): array { return $this->saved; }
}
final class CatalogTransactionSpy implements TransactionManagerInterface
{
    public int $calls = 0;
    public function transactional(callable $callback): mixed { $this->calls++; return $callback(); }
}
