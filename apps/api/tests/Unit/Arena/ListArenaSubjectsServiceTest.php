<?php
declare(strict_types=1);

namespace Tests\Unit\Arena;

use App\Application\Arena\Service\ListArenaSubjectsService;
use App\Domain\Taxonomy\Entity\TaxonomySubject;
use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class ListArenaSubjectsServiceTest extends TestCase
{
    public function testListsSubjectsWithQuestionCountsFromTheRepository(): void
    {
        $repository = new class implements TaxonomySubjectRepositoryInterface {
            public function save(TaxonomySubject $subject): void {} public function findById(string $id): ?TaxonomySubject { return null; } public function findByParentAndSlug(?string $parentId, string $slug): ?TaxonomySubject { return null; } public function findBySlug(string $slug): ?TaxonomySubject { return null; } public function findByComparableSlug(string $slug): ?TaxonomySubject { return null; } public function list(int $offset, int $limit): array { return [new TaxonomySubject('root', null, 'Informática', 'informatica', null, 0, true), new TaxonomySubject('child', 'root', 'Redes', 'redes', null, 1, true)]; } public function hasChildren(string $subjectId): bool { return $subjectId === 'root'; } public function questionCountsBySubjectId(): array { return ['root' => 9, 'child' => 4]; } public function count(): int { return 2; } public function ancestorIds(string $subjectId): array { return []; }
        };
        $result = (new ListArenaSubjectsService($repository))->list(1, 100);
        self::assertSame(2, $result['total']);
        self::assertSame('root', $result['items'][0]->id);
        self::assertSame(9, $result['items'][0]->questionCount);
        self::assertSame('root', $result['items'][1]->parentId);
        self::assertSame(4, $result['items'][1]->questionCount);
    }
}
