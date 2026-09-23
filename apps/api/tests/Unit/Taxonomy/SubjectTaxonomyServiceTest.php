<?php
declare(strict_types=1);

namespace Tests\Unit\Taxonomy;

use App\Domain\Taxonomy\Repository\TaxonomyHierarchyRepositoryInterface;
use App\Domain\Taxonomy\Service\SubjectTaxonomyService;
use PHPUnit\Framework\TestCase;

final class SubjectTaxonomyServiceTest extends TestCase
{
    public function testNormalizesTextAndCreatesStableSlug(): void
    {
        $service = new SubjectTaxonomyService();

        self::assertSame('sistema de nomes de domínio', $service->normalize("  Sistema   de nomes\n de Domínio  "));
        self::assertSame('sistema-de-nomes-de-dominio', $service->slug('Sistema de Nomes de Domínio'));
    }

    public function testRejectsMoveBelowOwnDescendant(): void
    {
        $hierarchy = new class implements TaxonomyHierarchyRepositoryInterface {
            public function ancestorIds(string $subjectId): array
            {
                return $subjectId === 'dns' ? ['protocols', 'networks', 'informatics'] : [];
            }
        };

        $this->expectException(\DomainException::class);
        (new SubjectTaxonomyService())->assertMoveDoesNotCreateCycle('networks', 'dns', $hierarchy);
    }
}
