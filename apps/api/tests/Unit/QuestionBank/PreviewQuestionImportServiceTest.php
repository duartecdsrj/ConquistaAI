<?php
declare(strict_types=1);

namespace Tests\Unit\QuestionBank;

use App\Application\QuestionBank\DTO\Request\PreviewQuestionImportRequestDto;
use App\Application\QuestionBank\Port\TransactionManagerInterface;
use App\Application\QuestionBank\Service\PreviewQuestionImportService;
use App\Application\QuestionBank\Service\QuestionImportValidationService;
use App\Domain\QuestionBank\Entity\QuestionImport;
use App\Domain\QuestionBank\Repository\QuestionImportRepositoryInterface;
use App\Infrastructure\Import\JsonCsvQuestionImportReader;
use PHPUnit\Framework\TestCase;

final class PreviewQuestionImportServiceTest extends TestCase
{
    public function testItPersistsJsonPreviewWithEachLine(): void
    {
        $repository = new InMemoryQuestionImports();
        $service = $this->service($repository);

        $report = $service->preview(new PreviewQuestionImportRequestDto('json', '[{"statement":"Questao","options":[{"id":"a"},{"id":"b"}],"correct_option":"a"}]', 'admin'));

        self::assertSame(1, $report->validRows);
        self::assertSame(0, $report->invalidRows);
        self::assertNotNull($report->importId);
        self::assertCount(1, $repository->imports);
        self::assertSame('admin', $repository->imports[0]->createdBy);
        self::assertCount(1, $repository->imports[0]->rows);
    }

    public function testItPersistsInvalidCsvRowsForLaterReport(): void
    {
        $repository = new InMemoryQuestionImports();
        $csv = "statement,options,correct_option\nQuestao,not-json,a\n";

        $report = $this->service($repository)->preview(new PreviewQuestionImportRequestDto('csv', $csv, 'admin'));

        self::assertSame(0, $report->validRows);
        self::assertSame(1, $report->invalidRows);
        self::assertFalse($repository->imports[0]->rows[0]->valid);
    }

    private function service(InMemoryQuestionImports $repository): PreviewQuestionImportService
    {
        return new PreviewQuestionImportService(
            new JsonCsvQuestionImportReader(),
            new QuestionImportValidationService(),
            $repository,
            new class implements TransactionManagerInterface {
                public function transactional(callable $callback): mixed { return $callback(); }
            },
        );
    }
}

final class InMemoryQuestionImports implements QuestionImportRepositoryInterface
{
    /** @var list<QuestionImport> */
    public array $imports = [];

    public function save(QuestionImport $import): void { $this->imports[] = $import; }

    public function findByIdForUser(string $id, string $userId): ?QuestionImport { return null; }
}
