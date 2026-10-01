<?php
declare(strict_types=1);

namespace Tests\Unit\Taxonomy;

use App\Application\Taxonomy\DTO\Request\ListTaxonomySubjectsRequestDto;
use App\Application\Taxonomy\Mapper\TaxonomySubjectResponseMapper;
use App\Application\Taxonomy\Service\ListTaxonomySubjectsService;
use App\Domain\Taxonomy\Entity\TaxonomySubject;
use App\Domain\Taxonomy\Repository\TaxonomySubjectRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class ListTaxonomySubjectsServiceTest extends TestCase
{
    public function testAggregatesDescendantQuestionsBeyondTheFormerFixedLimit(): void
    {
        $root = new TaxonomySubject('root', null, 'Informática', 'informatica', null, 0, true);
        $leaf = new TaxonomySubject('leaf', 'root', 'Redes', 'redes', null, 1, true);
        $repository = $this->createMock(TaxonomySubjectRepositoryInterface::class);
        $repository->method('count')->willReturn(1001);
        $repository->method('questionCountsBySubjectId')->willReturn(['leaf' => 7]);
        $repository->method('list')->willReturnCallback(
            static function (int $offset, int $limit) use ($root, $leaf): array {
                if ($offset === 0 && $limit === 1001) return [$root, $leaf];
                return [$root];
            },
        );
        $result = (new ListTaxonomySubjectsService($repository, new TaxonomySubjectResponseMapper()))->list(new ListTaxonomySubjectsRequestDto(1, 25));
        self::assertSame(1001, $result->total);
        self::assertSame(7, $result->items[0]->questionCount);
    }
}
